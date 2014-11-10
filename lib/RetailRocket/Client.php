<?php

namespace RetailRocket;

class Client {

    CONST NAME = 'retailrocket';

    public function __construct(array $config, \Logger\LoggerInterface $logger = null) {
        $this->config = array_merge([
            'apiUrl'         =>    \App::config()->partners['RetailRocket']['apiUrl'],
            'account'        =>    \App::config()->partners['RetailRocket']['account'],
            'timeout'        =>    \App::config()->partners['RetailRocket']['timeout'], //в секундах
            'logEnabled'     =>    false,
            'logDataEnabled' =>    false,
        ], $config);

        $this->logger = $logger;
    }


    public function query($action, $item_id = null) {
        $startedAt = \Debug\Timer::start('RetailRocket');

        $connection = $this->createResource($action, $item_id);
        $response = curl_exec($connection);
        try {
            if (curl_errno($connection) > 0) {
                throw new \RetailRocket\Exception(curl_error($connection), curl_errno($connection));
            }
            $info = curl_getinfo($connection);

            $this->logger->debug('RetailRocket response resource: ' . $connection, ['RetailRocket']);
            //$this->logger->debug('RetailRocket response info: ' . $this->encodeInfo($info), ['RetailRocket']);

            if ($this->config['logEnabled']) {
                $this->logger->info('Response ' . $connection . ' : ' . (is_array($info) ? json_encode($info, JSON_UNESCAPED_UNICODE) : $info), ['RetailRocket']);
            }

            if ($info['http_code'] >= 300) {
                throw new \RetailRocket\Exception(sprintf("Invalid http code: %d, \nResponse: %s", $info['http_code'], $response));
            }

            $responseDecoded = json_decode($response);
            curl_close($connection);

            $spend = \Debug\Timer::stop('RetailRocket');
            \App::logger()->info('End RetailRocket ' . $action . ' in ' . $spend, ['RetailRocket']);

            \App::logger()->info([
                'message' => 'End curl',
                'url'     => $info['url'],
                'data'    => [],
                'info'    => isset($info) ? $info : null,
                'header'  => isset($header) ? $header : null,
                'timeout' => $this->config['timeout'],
                'spend'   => $spend,
                'startAt' => $startedAt,
                'endAt'   => microtime(true),
            ], ['curl', 'RetailRocket']);

            return $responseDecoded;

        } catch (\RetailRocket\Exception $e) {
            curl_close($connection);
            $spend = \Debug\Timer::stop('RetailRocket');

            \App::logger()->error([
                'message' => 'Fail curl',
                'error'   => ['code' => $e->getCode(), 'message' => $e->getMessage()],
                'url'     => $this->config['apiUrl'] . $action . '/' . $this->config['account'] . '/' . $item_id,
                'data'    => [],
                'info'    => isset($info) ? $info : null,
                'header'  => isset($header) ? $header : null,
                'resonse' => isset($response) ? $response : null,
                'timeout' => $this->config['timeout'],
                'startAt' => $startedAt,
                'endAt'   => microtime(true),
                'spend'   => $spend,
            ], ['curl', 'RetailRocket']);

            //throw $e; // TODO: исправить на try-catch в местах использования
        }
    }



    public function createResource($action, $item_id = null) {
        $query = $this->config['apiUrl'] . $action . '/' . $this->config['account'] . '/' . $item_id;

        $user = \App::user();
        if ($user) {
            $uEntity = $user->getEntity();
            if ($uEntity) {
                $uid = $uEntity->getId();
                if ($uid) {
                    $query .= ((false === strpos($query, '?')) ? '?' : '&') . 'userId=' . $uid;
                }
            }
        }

        \App::logger()->info('Start RetailRocket ' . $action . ' query: ' . $query, ['RetailRocket']);

        $connection = curl_init();
        curl_setopt($connection, CURLOPT_HEADER, 0);
        curl_setopt($connection, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($connection, CURLOPT_NOSIGNAL, 1);
        curl_setopt($connection, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($connection, CURLOPT_TIMEOUT_MS, $this->config['timeout'] * 1000);
        curl_setopt($connection, CURLOPT_URL, $query);

        if ($this->config['logEnabled']) {
            $this->logger->info('Send RetailRocket request ' . $connection, ['RetailRocket']);
        }

        return $connection;


        /*$curlClient = new \Curl\Client(\App::logger());
        $resp = $curlClient->guery($query, [], $this->config['timeout']);
        return $resp;*/

    }


}