<?php

namespace Smartengine;

class Client {
    /* @var sfParameterHolder */
    private $config = null;

    /* @var sfFileLogger */
    private $logger = null;

    private $resources = array();

    public function __construct(array $config,  \Logger\LoggerInterface $logger = null)
    {
        $this->config = array_merge(array(
            'api_url'          => null,
            'api_key'          => null,
            'tenantid'         => null,
            'timeout'          => 0.5,
            'cert'             => null,
            'log_enabled'      => false,
            'log_data_enabled' => false,
        ), $config);

        $this->logger = $logger;
    }

    /**
     * Run synchronous query.
     *
     * @param $action
     * @param array $params
     * @throws SmartengineClientException
     * @return array
     */
    public function query($action, array $params = array())
    {
        \Debug\Timer::start('smartengine');

        $connection = $this->createResource($action, $params);
        $response = curl_exec($connection);
        try {
            if (curl_errno($connection) > 0) {
                throw new SmartengineClientException(curl_error($connection), curl_errno($connection));
            }
            $info = curl_getinfo($connection);
            $this->logger->debug('Smartengine response resource: ' . $connection);
            $this->logger->debug('Smartengine response info: ' . $this->encodeInfo($info));

            \Util\RequestLogger::getInstance()->addLog($info['url'], '', $info['total_time'], 'smartengine');

            if ($this->config['log_enabled']) {
                $this->logger->info('Response '.$connection.' : '.(is_array($info) ? json_encode($info, JSON_UNESCAPED_UNICODE) : $info));
            }
            if ($info['http_code'] >= 300) {
                throw new SmartengineClientException(sprintf("Invalid http code: %d, \nResponse: %s", $info['http_code'], $response));
            }

            if ($this->config['log_data_enabled']) {
                $this->logger->info('Response data: '.$response);
            }
            $responseDecoded = $this->decode($response);
            curl_close($connection);

            $spend = \Debug\Timer::stop('smartengine');
            \App::logger()->info('End smartengine ' . $action . ' in ' . $spend);

            return $responseDecoded;
        }
        catch (SmartengineClientException $e) {
            curl_close($connection);
            $spend = \Debug\Timer::stop('smartengine');
            \App::logger()->error('End smartengine ' . $action . ' in ' . $spend . ' get: ' . json_encode($params, JSON_UNESCAPED_UNICODE) . ' response: ' . json_encode($response, JSON_UNESCAPED_UNICODE) . ' with ' . $e);
            $this->logger->err($e->__toString());
            throw $e;
        }
    }

    /**
     * @param $action
     * @param array $params
     * @return resource
     */
    private function createResource($action, array $params = array())
    {
        foreach ($params as &$param) {
            $param = rawurlencode($param);
        } if (isset($param)) unset($param);

        $query = $this->config['api_url']
            . str_replace('.', '/', $action)
            . '?' . http_build_query(array_merge(array(
            'apikey'   => $this->config['api_key'],
            'tenantid' => $this->config['tenantid'],
        ), $params))
        ;
        \App::logger()->info('Start smartengine ' . $action . ' query: ' . $query);

        $connection = curl_init();
        curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, $this->config['ssl_verify']);
        if ($this->config['cert']) {
            curl_setopt($connection, CURLOPT_CAINFO, $this->config['cert']);
        }
        curl_setopt($connection, CURLOPT_HEADER, 0);
        curl_setopt($connection, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($connection, CURLOPT_TIMEOUT, $this->config['timeout']);
        curl_setopt($connection, CURLOPT_URL, $query);

        if ($this->config['log_enabled']) {
            $this->logger->info('Send smartengine requset '.$connection);
        }

        return $connection;
    }

    /**
     * @param $response
     * @return array
     * @throws SmartengineClientException
     */
    private function decode($response)
    {
        if (is_null($response)) {
            throw new SmartengineClientException('Response cannot be null');
        }

        $decoded = json_decode($response, true);
        // check json error
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
            $errorMessage = sprintf('Json error: "%s", Response: "%s"', $error, $response);

            throw new SmartengineClientException($errorMessage, $code);
        }

        return $decoded;
    }

    /**
     * @param $data
     * @return string
     */
    private function encode($data)
    {
        $data = json_encode($data);
        $data = preg_replace_callback(
            '/\\\u([0-9a-fA-F]{4})/',
            function($match)
            {
                return mb_convert_encoding("&#" . intval($match[1], 16) . ";", "UTF-8", "HTML-ENTITIES");
            },
            $data
        );

        return $data;
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


class SmartengineClientException extends \Exception {

}