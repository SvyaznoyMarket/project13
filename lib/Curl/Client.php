<?php

namespace Curl;

class Client {
    /** @var \Logger\LoggerInterface */
    private $logger;
    /** @var resource */
    private $isMultiple;
    /** @var array */
    private $successCallbacks = [];
    /** @var array */
    private $failCallbacks = [];
    /** @var array */
    private $resources = [];
    /** @var array */
    private $queries = [];
    /** @var array */
    private $queryIndex = [];
    /** @var bool */
    private $stillExecuting = false;

    /**
     * @param \Logger\LoggerInterface $logger
     */
    public function __construct(\Logger\LoggerInterface $logger) {
        $this->logger = $logger;
        $this->stillExecuting = false;
    }

    public function __clone() {
        $this->isMultiple = null;
        $this->successCallbacks = [];
        $this->failCallbacks = [];
        $this->resources = [];
        $this->queries = [];
        $this->queryIndex = [];
        $this->stillExecuting = false;
    }

    /**
     * @param string $url
     * @param array $data
     * @param float|null $timeout
     * @throws \RuntimeException
     * @throws \Exception|\RuntimeException
     * @return mixed
     */
    public function query($url, array $data = [], $timeout = null) {
        \Debug\Timer::start('curl');

        $connection = $this->create($url, $data, $timeout);
        $response = curl_exec($connection);
        try {
            if (curl_errno($connection) > 0) {
                throw new \RuntimeException(curl_error($connection), curl_errno($connection));
            }
            $info = curl_getinfo($connection);
            $this->logger->debug('Curl response resource: ' . $connection);
            $this->logger->debug('Curl response info: ' . $this->encodeInfo($info));
            $header = $this->header($response, true);

            \Util\RequestLogger::getInstance()->addLog($info['url'], $data, $info['total_time'], isset($header['X-Server-Name']) ? $header['X-Server-Name'] : 'unknown');

            if ($info['http_code'] >= 300) {
                throw new \RuntimeException(sprintf("Invalid http code: %d, \nResponse: %s", $info['http_code'], $response));
            }
            $this->logger->debug('Curl response: ' . $response);
            $decodedResponse = $this->decode($response);
            curl_close($connection);

            $spend = \Debug\Timer::stop('curl');
            $this->logger->info('End curl ' . $url . ' in ' . $spend);

            return $decodedResponse;
        } catch (\RuntimeException $e) {
            curl_close($connection);
            $spend = \Debug\Timer::stop('curl');
            $this->logger->error('Fail curl ' . $url . ' in ' . $spend . ((bool)$data ? (' ' . $this->encode($data)) : '') . ' response: ' . $this->encode($response) . ' with ' . $e);
            \App::exception()->add($e);

            throw $e;
        }
    }

    /**
     * @param string        $url
     * @param array         $data
     * @param callback      $successCallback
     * @param callback|null $failCallback
     * @param float|null    $timeout
     * @return bool
     */
    public function addQuery($url, array $data = [], $successCallback, $failCallback = null, $timeout = null) {
        if (!$this->isMultiple) {
            $this->isMultiple = curl_multi_init();
        }
        $resource = $this->create($url, $data, $timeout);
        if (0 !== curl_multi_add_handle($this->isMultiple, $resource)) {
            $this->logger->error('Adding multi query error: ' . curl_error($resource));
            return false;
        };
        $this->successCallbacks[(string)$resource] = $successCallback;
        $this->failCallbacks[(string)$resource] = $failCallback;
        $this->resources[] = $resource;

        /* нужно сохранить исходные данные для retry */
        $hash = md5($url . ':' . serialize($data));
        if (!isset($this->queries[$hash])) {
            $this->queries[$hash] = [
                'resources' => [
                    $resource,
                ],
                'query' => [
                    'url'  => $url,
                    'data' => $data,
                ],
            ];
        } else {
            $this->queries[$hash]['resources'][] = $resource;
        }
        $this->queryIndex[(string)$resource] = $hash;
        $this->stillExecuting = true;

        return true;
    }

    /**
     * @param float|null $retryTimeout
     * @param int $retryCount
     * @throws \RuntimeException
     * @throws \Exception
     */
    public function execute($retryTimeout = null, $retryCount = 0) {
        \Debug\Timer::start('curl');
        if (!$this->isMultiple) {
            $this->logger->error('No query to execute.');
            return;
        }

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
                    $this->logger->debug('Curl response done: ' . print_r($done, 1));
                    $handler = $done['handle'];

                    //$this->logger->info(microtime(true) . ': получен ответ на запрос ' . $this->queries[$this->queryIndex[(string)$handler]]['query']['url'] . '[' . (string)$handler . ']');
                    $this->logger->debug(microtime(true) . ': <- [' . (string)$handler . ']');

                    //удаляем запрос из массива запросов на исполнение и прерываем дублирующие запросы
                    foreach ($this->queries[$this->queryIndex[(string)$handler]]['resources'] as $resource) {
                        if ($resource !== $handler) {
                            curl_multi_remove_handle($this->isMultiple, $resource);
                        }
                    }

                    $info = curl_getinfo($handler);
                    $this->logger->debug('Curl response resource: ' . $handler);
                    $this->logger->debug('Curl response info: ' . $this->encodeInfo($info));
                    if (curl_errno($handler) > 0) {
                        $spend = \Debug\Timer::stop('curl');
                        \Util\RequestLogger::getInstance()->addLog($info['url'], $this->queries[$this->queryIndex[(string)$handler]]['query']['data'], $info['total_time'], 'multi(' . count($this->queries[$this->queryIndex[(string)$handler]]['resources']) . '): ' . 'unknown');
                        throw new \RuntimeException(curl_error($handler), curl_errno($handler));
                    }

                    try {
                        $content = curl_multi_getcontent($handler);
                        $header = $this->header($content, true);

                        \Util\RequestLogger::getInstance()->addLog($info['url'], $this->queries[$this->queryIndex[(string)$handler]]['query']['data'], $info['total_time'], 'multi(' . count($this->queries[$this->queryIndex[(string)$handler]]['resources']) . '): ' . (isset($header['X-Server-Name']) ? $header['X-Server-Name'] : 'unknown'));

                        unset($this->queries[$this->queryIndex[(string)$handler]]);

                        if ($info['http_code'] >= 300) {
                            throw new \RuntimeException(sprintf('Invalid http code %d, info: %s, response: %s', $info['http_code'], $this->encode($info), $content));
                        }

                        try {
                            $decodedResponse = $this->decode($content);
                        } catch (\Exception $e) {
                            $this->logger->error(sprintf('Json error for %s', (string)(isset($info['url']) ? $info['url'] : null)));
                            throw $e;
                        }
                        $this->logger->debug('Curl response data: ' . $this->encode($decodedResponse));
                        $callback = $this->successCallbacks[(string)$handler];
                        $callback($decodedResponse, (int)$handler);
                    } catch (\Exception $e) {
                        \App::exception()->add($e);
                        $this->logger->error($e);
                        $spend = \Debug\Timer::stop('curl');

                        $callback = $this->failCallbacks[(string)$handler];
                        if ($callback) {
                            $callback($e, (int)$handler);
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
                        if (count($query['resources']) < $retryCount) {
                            $isTryAvailable = true;
                            break;
                        }
                    }
                    if ($isTryAvailable && 0 !== $retryTimeout) {
                        $this->logger->debug(microtime(true) . ': ждем ответа или ' . $timeout . ' сек');
                        $ready = curl_multi_select($this->isMultiple, $timeout);
                    } else {
                        $this->logger->debug(microtime(true) . ':' . (0 === $retryTimeout ? '' : ' все попытки исчерпаны,') . ' ждем ответа');
                        $ready = curl_multi_select($this->isMultiple, 30);
                    }

                    if (0 === $ready) {
                        //Если случился timeout, то посылаем в ядро еще запросы
                        $this->logger->debug(microtime(true) . ': произошло прерывание по таймауту, в очереди ' . count($this->queries) . ' запроса(ов)');

                        foreach ($this->queries as $query) {
                            if (count($query['resources']) >= $retryCount) continue;
                            $this->logger->debug(microtime(true) . ': посылаю еще один запрос в ядро: ' . $query['query']['url']);
                            $this->addQuery(
                                $query['query']['url'],
                                $query['query']['data'],
                                $this->successCallbacks[(string)$query['resources'][0]],
                                isset($this->failCallbacks[(string)$query['resources'][0]]) ? $this->failCallbacks[(string)$query['resources'][0]] : null
                            );
                        }
                    }
                }
            } while ($this->stillExecuting);
        } catch (\Exception $e) {
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
            $this->logger->error('Error:' . (string)$error . ' Response: ' . print_r(isset($content) ? $content : null, true));
            $spend = \Debug\Timer::stop('curl');
            //throw $error;
        }

        $spend = \Debug\Timer::stop('curl');
        $this->logger->info('End curl executing in ' . $spend);
    }

    /**
     * @param string     $url
     * @param array      $data
     * @param float|null $timeout
     * @return resource
     */
    private function create($url, array $data = [], $timeout = null) {
        $this->logger->info('Start curl ' . $url . ((bool)$data ? ' ' . $this->encode($data) : '') . ($timeout ? (' timeout: ' . $timeout) : ''));

        $connection = curl_init();
        curl_setopt($connection, CURLOPT_HEADER, 1);
        curl_setopt($connection, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($connection, CURLOPT_URL, $url);
        curl_setopt($connection, CURLOPT_HTTPHEADER, ['X-Request-Id: '.\Util\RequestLogger::getInstance()->getId(), 'Expect:']);
        curl_setopt($connection, CURLOPT_ENCODING, 'gzip,deflate');

        if ($timeout) {
            curl_setopt($connection, CURLOPT_NOSIGNAL, 1);
            curl_setopt($connection, CURLOPT_TIMEOUT_MS, $timeout * 1000);
        }

        if ((bool)$data) {
            curl_setopt($connection, CURLOPT_POST, true);
            curl_setopt($connection, CURLOPT_POSTFIELDS, json_encode($data));
        }

        return $connection;
    }



    /**
     * @param string $plainResponse Ответ с заголовком (header) и телом (body)
     * @param bool   $isUpdateResponse Нужно ли вырезать из ответа заголовок (header), если true, то в $plainResponse по окончании работы будет содержаться тело (body)
     * @return array
     * @throws \RuntimeException
     */
    private function header(&$plainResponse, $isUpdateResponse = true) {
        if (is_null($plainResponse)) {
            throw new \RuntimeException('Response cannot be null');
        }

        $header = [];
        $response = explode("\r\n\r\n", $plainResponse, 2);
        if ($isUpdateResponse) $plainResponse = isset($response[1]) ? $response[1] : null;

        $plainHeader = explode("\r\n", $response[0]);
        foreach ($plainHeader as $line) {
            $pos = strpos($line, ':');
            if ($pos) {
                $key = substr($line, 0, $pos);
                $value = trim(substr($line, $pos + 1));
                $header[$key] = $value;
            } else {
                $header[] = $line;
            }
        }

        return $header;
    }

    /**
     * @param string $response Тело ответа без заголовка (header)
     * @throws \RuntimeException
     * @throws Exception
     * @return mixed
     */
    private function decode($response) {
        if (is_null($response)) {
            throw new \RuntimeException('Пустой ответ');
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
            $e = new \RuntimeException(sprintf('Json error: "%s", Response: "%s"', $error, $response), $code);
            \App::exception()->add($e);
            throw $e;
        }

        if (is_array($decoded)) {
            if (array_key_exists('error', $decoded)) {
                $e = new Exception(
                    $this->encode($decoded),
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
     * @param array $data
     * @return string
     */
    private function encode($data) {
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param array $info
     * @return string
     */
    private function encodeInfo($info) {
        return $this->encode(array_intersect_key($info, array_flip([
            'content_type', 'http_code', 'header_size', 'request_size',
            'redirect_count', 'total_time', 'namelookup_time', 'connect_time', 'pretransfer_time', 'size_upload',
            'size_download', 'speed_download',
            'starttransfer_time', 'redirect_time', 'certinfo', 'redirect_url'
        ])));
    }
}