<?php

namespace Core;

class ClientV2 implements ClientInterface
{
    private $config;
    /** @var \Logger\LoggerInterface */
    private $logger;
    /** @var resource */
    private $isMultiple;
    private $callbacks = array();
    private $resources = array();

    public function __construct(array $config, \Logger\LoggerInterface $logger = null)
    {
        $this->config = array_merge(array(
            'client_id' => null,
        ), $config);
        $this->logger = $logger;
    }

    public function query($action, array $params = array(), array $data = array()) {
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
            \App::logger()->error('End core ' . $action . ' in ' . $spend . ' get: ' . json_encode($params) . ' post: ' . json_encode($data) . ' response: ' . json_encode($response, true) . ' with ' . $e);

            throw $e;
        }
    }

    public function addQuery($action, array $params = array(), array $data = array(), $callback) {
        if (!$this->isMultiple) {
            $this->isMultiple = curl_multi_init();
        }
        $resource = $this->createResource($action, $params, $data);
        curl_multi_add_handle($this->isMultiple, $resource);
        $this->callbacks[(string)$resource] = $callback;
        $this->resources[] = $resource;
    }

    public function execute() {
        if (!$this->isMultiple) {
            throw new \RuntimeException('No query to execute.');
        }

        $active = null;
        $error = null;
        try {
            do {
                $code = curl_multi_exec($this->isMultiple, $stillExecuting);
                if ($code == CURLM_OK) {
                    // if one or more descriptors is ready, read content and run callbacks
                    while ($done = curl_multi_info_read($this->isMultiple)) {
                        $this->logger->debug('Core response done: ' . print_r($done, 1));
                        $handler = $done['handle'];
                        $info = curl_getinfo($handler);
                        $this->logger->debug('Core response resource: ' . $handler);
                        $this->logger->debug('Core response info: ' . $this->encodeInfo($info));
                        if (curl_errno($handler) > 0) {
                            throw new \RuntimeException(curl_error($handler), curl_errno($handler));
                        }
                        $content = curl_multi_getcontent($handler);
                        if ($info['http_code'] >= 300) {
                            throw new \RuntimeException(sprintf("Invalid http code: %d, \nResponse: %s", $info['http_code'], $content));
                        }
                        $decodedResponse = $this->decode($content);
                        $this->logger->debug('Core response data: ' . $this->encode($decodedResponse));
                        $callback = $this->callbacks[(string)$handler];
                        $callback($decodedResponse);
                    }
                } elseif ($code != CURLM_CALL_MULTI_PERFORM) {
                    throw new \RuntimeException("multi_curl failure [$code]");
                }
            } while ($stillExecuting);
        } catch (Exception $e) {
            $error = $e;
        }
        // clear multi container
        foreach ($this->resources as $resource) {
            curl_multi_remove_handle($this->isMultiple, $resource);
        }
        curl_multi_close($this->isMultiple);
        $this->isMultiple = null;
        $this->callbacks = array();
        $this->resources = array();
        if (!is_null($error)) {
            $this->logger->error('Error:' . (string)$error . 'Response: ' . print_r(isset($content) ? $content : null, true));
            throw $error;
        }
    }

    private function createResource($action, array $params = array(), array $data = array()) {
        $isPostMethod = !empty($data);

        $query = $this->config['url']
            . $action
            . '?' . http_build_query(array_merge($params, array('client_id' => $this->config['client_id'])));

        \App::logger()->info('Start core ' . $action . ' query: ' . $query);

        $this->logger->info('Send core requset ' . ($isPostMethod ? 'post' : 'get') . ': ' . $query);
        if ($data) {
            $this->logger->info('Request post:' . $this->encode($data));
        }

        $connection = curl_init();
        curl_setopt($connection, CURLOPT_HEADER, 0);
        curl_setopt($connection, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($connection, CURLOPT_URL, $query);

        if ($isPostMethod) {
            curl_setopt($connection, CURLOPT_POST, true);
            curl_setopt($connection, CURLOPT_POSTFIELDS, json_encode($data));
        }

        return $connection;
    }

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
            throw new \RuntimeException($message, $code);
        }

        if (is_array($decoded) && array_key_exists('error', $decoded)) {
            $e = new \RuntimeException(
                (string)$decoded['error']['message'] . ' ' . $this->encode($decoded),
                (int)$decoded['error']['code']
            );

            throw $e;
        }
        if (array_key_exists('result', $decoded)) {
            $decoded = $decoded['result'];
        }

        return $decoded;
    }

    private function encode($data)
    {
        //return json_encode($data, JSON_UNESCAPED_UNICODE);
        return json_encode($data);
    }

    private function encodeInfo($info)
    {
        return $this->encode(array_intersect_key($info, array_flip(array(
            'content_type', 'http_code', 'header_size', 'request_size',
            'redirect_count', 'total_time', 'namelookup_time', 'connect_time', 'pretransfer_time', 'size_upload',
            'size_download', 'speed_download',
            'starttransfer_time', 'redirect_time', 'certinfo', 'redirect_url'
        ))));
    }
}