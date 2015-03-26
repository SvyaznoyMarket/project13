<?php

namespace Content;

class Client {
    /** @var array */
    private $config;
    /** @var \Curl\Client */
    private $curl;

    /**
     * @param array        $config
     * @param \Curl\Client $curl
     */
    public function __construct(array $config, \Curl\Client $curl) {
        $this->config = array_merge([
            'url'            => null,
            'timeout'        => null,
            'throwException' => null,
            'retryTimeout'   => null,
            'retryCount'     => null,
        ], $config);

        $this->curl = $curl;
    }

    public function __clone() {
        $this->curl = clone $this->curl;
    }

    /**
     * @param $action
     * @param array $data
     * @param null $throwException
     * @param null $retryTimeout
     * @return null
     */
    public function query ($action, array $data = [], $throwException = null, $retryTimeout = null) {
        \Debug\Timer::start('content');
        //\App::logger()->debug('Start content request ' . $action, ['content']);

        if (null === $throwException) {
            $throwException = $this->config['throwException'];
        }
        if (null === $retryTimeout) {
            $retryTimeout = $this->config['retryTimeout']['default'];
        }

        $url = $this->config['url'] . $action . '?json=1';
        $response = null;
        $this->curl->addQuery($url, $data, function ($data) use (&$response, $action) {
            $response = $data;
            $spend = \Debug\Timer::stop('content');
            //\App::logger()->debug('End content request ' . $action . ' in ' . $spend, ['content']);
        }, function ($e) use ($action, $throwException) {
            if (false === $throwException) {
                \App::exception()->remove($e);
            }
            $spend = \Debug\Timer::stop('content');
            //\App::logger()->debug('Fail content request ' . $action . ' in ' . $spend . ' with ' . $e, ['content']);
        }, $this->config['timeout']);
        $this->curl->execute($retryTimeout, $this->config['retryCount']);

        return $response;
    }

    /**
     * @param $action
     * @param array         $data
     * @param callback      $successCallback
     * @param callback|null $failCallback
     * @param float|null    $timeout
     * @return bool
     */
    public function addQuery($action, $data = [], $successCallback, $failCallback = null, $timeout = null) {
        \Debug\Timer::start('content');

        if (null === $timeout) {
            $timeout = $this->config['timeout'];
        }
        if ((null === $failCallback) && !$this->config['throwException']) { // если не задана функция при падении и в настройках не указано выбрасывать 500-й статус
            $failCallback = function(\Exception $e) {
                \App::exception()->remove($e);
            };
        }

        $result = $this->curl->addQuery($this->config['url'] . $action  . '?json=1', $data, $successCallback, $failCallback, $timeout);

        \Debug\Timer::stop('content');

        return $result;
    }

    /**
     * @param int $retryTimeout
     * @param int $retryCount
     * @return void
     */
    public function execute($retryTimeout = null, $retryCount = null) {
        \Debug\Timer::start('content');

        if (null === $retryTimeout) {
            $retryTimeout = isset($this->config['retryTimeout']['default']) ? $this->config['retryTimeout']['default'] : 0;
        }
        if (null === $retryCount) {
            $retryCount = $this->config['retryCount'];
        }

        $this->curl->execute($retryTimeout, $retryCount);

        \Debug\Timer::stop('content');
    }
}