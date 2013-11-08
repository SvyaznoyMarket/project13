<?php

namespace ShopScript;

class Client {
    /** @var array */
    private $config;
    /** @var \Curl\Client */
    private $curl;

    /**
     * @param array $config
     * @param \Curl\Client $curl
     */
    public function __construct(array $config, \Curl\Client $curl) {
        $this->config = array_merge([
            'url'          => null,
            'timeout'      => null,
            'retryTimeout' => null,
            'retryCount'   => null,
        ], $config);

        $this->curl = $curl;
    }

    public function __clone() {
        $this->curl = clone $this->curl;
    }

    /**
     * @param string     $action
     * @param array      $params
     * @param array      $data
     * @param float|null $timeout
     * @return mixed
     */
    public function query($action, array $params = [], array $data = [], $timeout = null) {
        \Debug\Timer::start('ShopScript');

        if (isset(\App::config()->shopScript['user'])) {
            $data['http_user'] = \App::config()->shopScript['user'];
        }

        if (isset(\App::config()->shopScript['password'])) {
            $data['http_password'] = \App::config()->shopScript['password'];
        }

        if (null === $timeout) {
            $timeout = $this->config['timeout'];
        }

        $this->curl->addQuery($this->getUrl($action, $params), $data, function($data) use (&$result) {
            $result = $data;
        }, function($e) {
            \App::exception()->remove($e);
            \App::logger()->info('Fail ShopScript request with ' . $e, ['shopScript']);
        }, $timeout);

        $this->curl->execute($this->config['retryTimeout']['default'], $this->config['retryCount']);

        \Debug\Timer::stop('ShopScript');

        return $result;
    }

    /**
     * @param string        $action
     * @param array         $params
     * @param array         $data
     * @param callback      $successCallback
     * @param callback|null $failCallback
     * @param float|null    $timeout
     * @return bool
     */
    public function addQuery($action, array $params = [], array $data = [], $successCallback, $failCallback = null, $timeout = null) {
        \Debug\Timer::start('ShopScript');

        if (null === $timeout) {
            $timeout = $this->config['timeout'];
        }

        if (isset(\App::config()->shopScript['user'])) {
            $data['http_user'] = \App::config()->shopScript['user'];
        }
        if (isset(\App::config()->shopScript['password'])) {
            $data['http_password'] = \App::config()->shopScript['password'];
        }

        $result = $this->curl->addQuery($this->getUrl($action, $params), $data, $successCallback, function($e) {
            \App::exception()->remove($e);
            \App::logger()->info('Fail ShopScript request with ' . $e, ['shopScript']);
        }, $timeout);

        \Debug\Timer::stop('ShopScript');

        return $result;
    }

    /**
     * @param int $retryTimeout
     * @param int $retryCount
     * @return void
     */
    public function execute($retryTimeout = null, $retryCount = null) {
        \Debug\Timer::start('ShopScript');

        if (null === $retryTimeout) {
            $retryTimeout = isset($this->config['retryTimeout']['default']) ? $this->config['retryTimeout']['default'] : 0;
        }
        if (null === $retryCount) {
            $retryCount = $this->config['retryCount'];
        }

        $this->curl->execute($retryTimeout, $retryCount);

        \Debug\Timer::stop('ShopScript');
    }

    /**
     * @param string $action
     * @param array  $params
     * @return resource
     */
    private function getUrl($action, array $params = []) {
        return $this->config['url']
            . $action
            . '?' . http_build_query($params);
    }
}