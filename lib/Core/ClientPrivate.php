<?php

namespace Core;

class ClientPrivate implements ClientInterface {
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
            'client_id'    => null,
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
        \Debug\Timer::start('core-private');

        $response = $this->curl->query($this->getUrl($action, $params), $data, $timeout);

        \Debug\Timer::stop('core-private');

        return $response;
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
        \Debug\Timer::start('core-private');

        if (null === $timeout) {
            $timeout = $this->config['timeout'];
        }

        $result = $this->curl->addQuery($this->getUrl($action, $params), $data, $successCallback, $failCallback, $timeout);

        \Debug\Timer::stop('core-private');

        return $result;
    }

    /**
     * @param int $retryTimeout
     * @param int $retryCount
     * @return void
     */
    public function execute($retryTimeout = null, $retryCount = null) {
        \Debug\Timer::start('core-private');

        if (null === $retryTimeout) {
            $retryTimeout = isset($this->config['retryTimeout']['default']) ? $this->config['retryTimeout']['default'] : 0;
        }
        if (null === $retryCount) {
            $retryCount = $this->config['retryCount'];
        }

        $this->curl->execute($retryTimeout, $retryCount);

        \Debug\Timer::stop('core-private');
    }

    /**
     * @param string $action
     * @param array  $params
     * @return resource
     */
    private function getUrl($action, array $params = []) {
        return $this->config['url']
            . $action
            . '?' . http_build_query(array_merge($params, ['client_id' => $this->config['client_id']]));
    }
}