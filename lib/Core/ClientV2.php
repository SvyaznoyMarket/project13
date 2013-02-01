<?php

namespace Core;

class ClientV2 implements ClientInterface
{
    private $config;
    /** @var \Logger\LoggerInterface */
    private $logger;
    /** @var resource */
    private $isMultiple;
    private $successCallbacks = [];
    private $failCallbacks = [];
    private $resources = [];
    private $queries = [];
    private $queryIndex = [];
    /** @var bool */
    private $stillExecuting = false;

    public function __construct(array $config, \Logger\LoggerInterface $logger = null) {
        $this->config = array_merge(array(
            'client_id' => null,
        ), $config);
        $this->logger = $logger;

        $this->stillExecuting = false;
    }

    /**
     * @param $action
     * @param array $params
     * @param array $data
     * @return mixed
     * @throws \RuntimeException
     */
    public function query($action, array $params = [], array $data = []) {
        \Debug\Timer::start('core');

        $connection = $this->createResource($action, $params, $data);
        $response = curl_exec($connection);
        try {
            if (curl_errno($connection) > 0) {
                throw new \RuntimeException(curl_error($connection), curl_errno($connection));
            }
            $info = curl_getinfo($connection);
            $this->logger->debug('Core response resource: ' . $connection);
            $this->logger->debug('Core response info: ' . $this->encodeInfo($info));
            $header = $this->getHeader($response, true);

            \Util\RequestLogger::getInstance()->addLog($info['url'], $data, $info['total_time'], isset($header['X-Server-Name']) ? $header['X-Server-Name'] : 'unknown');

            if ($info['http_code'] >= 300) {
                throw new \RuntimeException(sprintf("Invalid http code: %d, \nResponse: %s", $info['http_code'], $response));
            }
            $this->logger->debug('Core response: ' . $response);
            $responseDecoded = $this->decode($response);
            curl_close($connection);

            $spend = \Debug\Timer::stop('core');
            \App::logger()->info('End core ' . $action . ' in ' . $spend);

            return $responseDecoded;
        } catch (\RuntimeException $e) {
            curl_close($connection);
            $spend = \Debug\Timer::stop('core');
            \App::logger()->error('End core ' . $action . ' in ' . $spend . ' get: ' . json_encode($params, JSON_UNESCAPED_UNICODE) . ' post: ' . json_encode($data, JSON_UNESCAPED_UNICODE) . ' response: ' . json_encode($response, JSON_UNESCAPED_UNICODE) . ' with ' . $e);
            \App::exception()->add($e);

            throw $e;
        }
    }

    public function addQuery($action, array $params = [], array $data = [], $successCallback, $failCallback = null) {
        if (!$this->isMultiple) {
            $this->isMultiple = curl_multi_init();
        }
        $resource = $this->createResource($action, $params, $data);
        if (0 !== curl_multi_add_handle($this->isMultiple, $resource)) {
            $this->logger->error('Adding multi query error: ' . curl_error($resource));
            return false;
        };
        $this->successCallbacks[(string)$resource] = $successCallback;
        $this->failCallbacks[(string)$resource] = $failCallback;
        $this->resources[] = $resource;

        /* нужно сохранить исходные данные для retry */
        $hash = md5($action . serialize($params) . serialize($data));
        if (!isset($this->queries[$hash])) {
            $this->queries[$hash] = array(
                'resources' => array(
                    $resource,
                ),
                'query' => array(
                    'action' => $action,
                    'params' => $params,
                    'data'   => $data,
                ),
            );
        } else {
            $this->queries[$hash]['resources'][] = $resource;
        }
        $this->queryIndex[$resource] = $hash;

        $this->stillExecuting = true;
    }

    /**
     * @param int $retryTimeout
     * @throws \RuntimeException
     */
    public function execute($retryTimeout = null) {
        \Debug\Timer::start('core');
        if (!$this->isMultiple) {
            $this->logger->error('No query to execute.');
            return;
        }

        if (null === $retryTimeout) $retryTimeout = isset($this->config['retryTimeout']['default']) ? $this->config['retryTimeout']['default'] : 0;
        $active = null;
        $error = null;
        try {
            $absoluteTimeout = microtime(true);
            do {
                if ($absoluteTimeout <= microtime(true)) {
                    $absoluteTimeout += $retryTimeout;
                    $this->logger->debug(microtime(true) . ': Слудеющий таймаут должен сработать в ' . $absoluteTimeout);
                }
                do {
                    $code = curl_multi_exec($this->isMultiple, $stillExecuting);
                    $this->stillExecuting = $stillExecuting;
                } while ($code == CURLM_CALL_MULTI_PERFORM);

                // if one or more descriptors is ready, read content and run callbacks
                while ($done = curl_multi_info_read($this->isMultiple)) {
                    $this->logger->debug('Core response done: ' . print_r($done, 1));
                    $handler = $done['handle'];

                    //$this->logger->info(microtime(true) . ': получен ответ на запрос ' . $this->queries[$this->queryIndex[$handler]]['query']['action'] . '[' . (string)$handler . ']');
                    $this->logger->debug(microtime(true) . ': <- [' . (string)$handler . ']');

                    //удаляем запрос из массива запросов на исполнение и прерываем дублирующие запросы
                    foreach ($this->queries[$this->queryIndex[$handler]]['resources'] as $resource) {
                        if ($resource !== $handler) {
                            curl_multi_remove_handle($this->isMultiple, $resource);
                        }
                    }

                    $info = curl_getinfo($handler);
                    $this->logger->debug('Core response resource: ' . $handler);
                    $this->logger->debug('Core response info: ' . $this->encodeInfo($info));
                    if (curl_errno($handler) > 0) {
                        $spend = \Debug\Timer::stop('core');
                        \Util\RequestLogger::getInstance()->addLog($info['url'], $this->queries[$this->queryIndex[$handler]]['query']['data'], $info['total_time'], 'multi(' . count($this->queries[$this->queryIndex[$handler]]['resources']) . ' try(s)): ' . 'unknown');
                        throw new \RuntimeException(curl_error($handler), curl_errno($handler));
                    }
                    $content = curl_multi_getcontent($handler);
                    $header = $this->getHeader($content, true);

                    \Util\RequestLogger::getInstance()->addLog($info['url'], $this->queries[$this->queryIndex[$handler]]['query']['data'], $info['total_time'], 'multi(' . count($this->queries[$this->queryIndex[$handler]]['resources']) . ' try(s)): ' . (isset($header['X-Server-Name']) ? $header['X-Server-Name'] : 'unknown'));

                    unset($this->queries[$this->queryIndex[$handler]]);

                    if ($info['http_code'] >= 300) {
                        $spend = \Debug\Timer::stop('core');
                        throw new \RuntimeException(sprintf("Invalid http code: %d, \nResponse: %s", $info['http_code'], $content));
                    }
                    try {
                        $decodedResponse = $this->decode($content);
                        $this->logger->debug('Core response data: ' . $this->encode($decodedResponse));
                        $callback = $this->successCallbacks[(string)$handler];
                        $callback($decodedResponse);
                    } catch (\Exception $e) {
                        \App::exception()->add($e);
                        \App::logger()->error($e);

                        $callback = $this->failCallbacks[(string)$handler];
                        if ($callback) {
                            $callback($e);
                        }
                    }
                }
                if ($stillExecuting) {
                    $timeout = $absoluteTimeout - microtime(true);
                    if (0 >= $timeout) {
                        $timeout += $retryTimeout;
                        $absoluteTimeout += $retryTimeout;
                    }
                    $isTryAvailable = false;
                    foreach ($this->queries as $query) {
                        if (count($query['resources']) < $this->config['retryCount']) {
                            $isTryAvailable = true;
                            break;
                        }
                    }
                    if ($isTryAvailable && 0 !== $retryTimeout) {
                        $this->logger->debug(microtime(true) . ': ждем ответа или ' . $timeout . ' сек');
                        $ready = curl_multi_select($this->isMultiple, $timeout);
                    } else {
                        $this->logger->debug(microtime(true) . ':' . ( 0 === $retryTimeout ? '' : ' все попытки исчерпаны,') . ' ждем ответа');
                        $ready = curl_multi_select($this->isMultiple, 30);
                    }

                    if (0 === $ready) {
                        //Если случился timeout, то посылаем в ядро еще запросы
                        $this->logger->debug(microtime(true) . ': произошло прерывание по таймауту, в очереди ' . count($this->queries) . ' запроса(ов)');

                        foreach ($this->queries as $query) {
                            if (count($query['resources']) >= $this->config['retryCount']) continue;
                            $this->logger->debug(microtime(true) . ': посылаю еще один запрос в ядро: ' . $query['query']['action']);
                            $this->addQuery(
                                $query['query']['action'],
                                $query['query']['params'],
                                $query['query']['data'],
                                $this->successCallbacks[(string)$query['resources'][0]],
                                isset($this->failCallbacks[(string)$query['resources'][0]]) ? $this->failCallbacks[(string)$query['resources'][0]] : null
                            );
                        }
                    }
                }
            } while ($this->stillExecuting);
        } catch (Exception $e) {
            $error = $e;
        }
        // clear multi container
        foreach ($this->resources as $resource) {
            curl_multi_remove_handle($this->isMultiple, $resource);
        }
        curl_multi_close($this->isMultiple);
        $this->isMultiple = null;
        $this->successCallbacks = [];
        $this->failCallbacks = [];
        $this->resources = [];
        if (!is_null($error)) {
            \App::exception()->add($error);
            $this->logger->error('Error:' . (string)$error . 'Response: ' . print_r(isset($content) ? $content : null, true));
            $spend = \Debug\Timer::stop('core');
            //throw $error;
        }

        $spend = \Debug\Timer::stop('core');
        \App::logger()->info('End core execute in ' . $spend);
    }

    /**
     * @param string $action
     * @param array  $params
     * @param array  $data
     * @return resource
     */
    private function createResource($action, array $params = [], array $data = []) {
        $isPostMethod = !empty($data);

        $query = $this->config['url']
            . $action
            . '?' . http_build_query(array_merge($params, ['client_id' => $this->config['client_id']]));

        \App::logger()->info('Start core ' . $action . ' query: ' . $query);

        $this->logger->info('Send core requset ' . ($isPostMethod ? 'post' : 'get') . ': ' . $query);
        if ($data) {
            $this->logger->info('Request post:' . $this->encode($data));
        }

        $connection = curl_init();
        curl_setopt($connection, CURLOPT_HEADER, 1);
        curl_setopt($connection, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($connection, CURLOPT_URL, $query);
        curl_setopt($connection, CURLOPT_HTTPHEADER, ['X-Request-Id: '.\Util\RequestLogger::getInstance()->getId(), 'Expect:']);
        curl_setopt($connection, CURLOPT_ENCODING, 'gzip,deflate');

        if ($isPostMethod) {
            curl_setopt($connection, CURLOPT_POST, true);
            curl_setopt($connection, CURLOPT_POSTFIELDS, json_encode($data));
        }

        return $connection;
    }

    /**
     * @param string $response Тело ответа без заголовка (header)
     * @throws \RuntimeException
     * @throws Exception
     * @return mixed
     */
    private function decode($response) {
        if (is_null($response)) {
            throw new \RuntimeException('Response cannot be null');
        }

        $decoded = json_decode($response, true);
        if ($code = json_last_error()) {
            switch ($code) {
                case JSON_ERROR_DEPTH:
                    $error = 'Maximum stack depth exceeded';
                    break;
                case JSON_ERROR_STATE_MISMATCH:
                    $error = 'Underflow or the modes mismatch';
                    break;
                case JSON_ERROR_CTRL_CHAR:
                    $error = 'Unexpected control character found';
                    break;
                case JSON_ERROR_SYNTAX:
                    $error = 'Syntax error, malformed JSON';
                    break;
                case JSON_ERROR_UTF8:
                    $error = 'Malformed UTF-8 characters, possibly incorrectly encoded';
                    break;
                default:
                    $error = 'Unknown error';
                    break;
            }
            $message = sprintf('Json error: "%s", Response: "%s"', $error, $response);
            $e = new \RuntimeException($message, $code);
            \App::exception()->add($e);
            throw $e;
        }

        if (is_array($decoded)) {
            if (array_key_exists('error', $decoded)) {
                $message = $decoded['error']['message'] . ' ' . $this->encode($decoded);

                $e = new Exception(
                    $message,
                    (int)$decoded['error']['code']
                );

                /**
                 * $e->setContent нужен для того, чтобы сохранять ошибки от /v2/order/calc-tmp:
                 *   кроме error.code и error.message возвращается массив error.product_error_list
                 */
                $e->setContent($decoded['error']);

                throw $e;
            }

            if (array_key_exists('result', $decoded)) {
                $decoded = $decoded['result'];
            }
        }

        return $decoded;
    }

    /**
     * @param string $plainResponse Ответ с заголовком (header) и телом (body)
     * @param bool $isUpdateResponse Нужно ли вырезать из ответа заголовок (header), если true, то в $plainResponse по окончании работы будет содержаться тело (body)
     * @return array
     * @throws \RuntimeException
     */
    private function getHeader(&$plainResponse, $isUpdateResponse = true) {
        if (is_null($plainResponse)) {
            throw new \RuntimeException('Response cannot be null');
        }

        $header = [];
        $response = explode("\r\n\r\n", $plainResponse);
        if ($isUpdateResponse) $plainResponse = isset($response[1]) ? $response[1] : null;

        $plainHeader = explode("\r\n", $response[0]);
        foreach ($plainHeader as $line) {
            $pos = strpos($line, ':');
            if ($pos) {
                $key = substr($line, 0, $pos);
                $value = trim(substr($line, $pos + 1));
                $header[$key] = $value;
            }
            else {
                $header[] = $line;
            }
        }

        return $header;
    }

    private function encode($data) {
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    private function encodeInfo($info) {
        return $this->encode(array_intersect_key($info, array_flip(array(
            'content_type', 'http_code', 'header_size', 'request_size',
            'redirect_count', 'total_time', 'namelookup_time', 'connect_time', 'pretransfer_time', 'size_upload',
            'size_download', 'speed_download',
            'starttransfer_time', 'redirect_time', 'certinfo', 'redirect_url'
        ))));
    }
}