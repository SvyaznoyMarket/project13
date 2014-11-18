<?php

namespace RetailRocket;

class Client {

    CONST NAME = 'retailrocket';

    public function __construct(array $config, \Logger\LoggerInterface $logger = null) {
        $this->config = array_merge([
            'apiUrl'         =>    \App::config()->partners['RetailRocket']['apiUrl'],
            'account'        =>    \App::config()->partners['RetailRocket']['account'],
            'timeout'        =>    \App::config()->partners['RetailRocket']['timeout'], //в секундах
        ], $config);

        $this->logger = $logger;

        $this->curl = \App::curl();
    }


    public function __clone() {
        $this->curl = clone $this->curl;
    }

    /**
     * @param string     $action
     * @param string|null $itemId
     * @param array      $params
     * @param array      $data
     * @param float|null $timeout
     * @return mixed
     */
    public function query($action, $itemId = null, array $params = [], array $data = [], $timeout = null) {
        \Debug\Timer::start('RetailRocket');

        if (null === $timeout) {
            $timeout = $this->config['timeout'];
        }

        $this->curl->addQuery(
            $this->getUrl($action, $itemId, $params),
            $data,
            function($data) use (&$result) {
                $result = $data;
            },
            function($e) {
                \App::exception()->remove($e);
                \App::logger()->warn(['error' => $e], ['RetailRocket']);
            },
            $timeout
        );

        $this->curl->execute();

        \Debug\Timer::stop('RetailRocket');

        return $result;
    }

    /**
     * @param string $action
     * @param string|null $itemId
     * @param array $params
     * @param array $data
     * @param callback $successCallback
     * @param callback|null $failCallback
     * @param float|null $timeout
     * @return bool
     */
    public function addQuery($action, $itemId = null, array $params = [], array $data = [], $successCallback, $failCallback = null, $timeout = null) {
        \Debug\Timer::start('RetailRocket');

        if (null === $timeout) {
            $timeout = $this->config['timeout'];
        }

        $this->curl->addQuery(
            $this->getUrl($action, $itemId, $params),
            $data,
            $successCallback,
            function($e) {
                \App::exception()->remove($e);
                \App::logger()->warn(['error' => $e], ['RetailRocket']);
            },
            $timeout
        );

        \Debug\Timer::stop('RetailRocket');
    }

    /**
     * @param int $retryTimeout
     * @param int $retryCount
     * @return void
     */
    public function execute($retryTimeout = null, $retryCount = null) {
        \Debug\Timer::start('RetailRocket');

        $this->curl->execute($retryTimeout, $retryCount);

        \Debug\Timer::stop('RetailRocket');
    }

    /**
     * @param string $action
     * @param string|null $itemId
     * @param array $params
     * @return resource
     */
    private function getUrl($action, $itemId = null, array $params = []) {
        return
            $this->config['apiUrl']
            . $action
            . '/' . $this->config['account']
            . ($itemId ? ('/' . $itemId) : '')
            . '?' . http_build_query($params)
        ;
    }
}