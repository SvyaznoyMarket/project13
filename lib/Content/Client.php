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
        \App::logger()->debug('Start content request ' . $action, ['content']);

        if (null === $this->config['throwException']) {
            $throwException = $this->config['throwException'];
        }
        if (null === $retryTimeout) {
            $retryTimeout = \App::config()->coreV2['retryTimeout']['short'];
        }

        $url = $this->config['url'] . $action . '?json=1';
        $response = null;
        $this->curl->addQuery($url, $data, function ($data) use (&$response, $action) {
            $response = $data;
            $spend = \Debug\Timer::stop('content');
            \App::logger()->debug('End content request ' . $action . ' in ' . $spend, ['content']);
        }, function ($e) use ($action, $throwException) {
            if (false === $throwException) {
                \App::exception()->remove($e);
            }
            $spend = \Debug\Timer::stop('content');
            \App::logger()->debug('Fail content request ' . $action . ' in ' . $spend . ' with ' . $e, ['content']);
        }, $this->config['timeout']);
        $this->curl->execute($retryTimeout, \App::config()->coreV2['retryCount']);

        return $response;
    }
}