<?php

namespace DataStore;

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
     * @param string $path
     * @return array|null
     * @throws \Exception
     */
    public function query($path) {
        \Debug\Timer::start('data-store');
        //\App::logger()->info('Start data-store request ' . $path, ['data-store']);

        $response = null;
        // локальный файл
        if (0 === strpos($path, '/')) {
            $file = \App::config()->dataDir . '/data-store' . $path;
            $response = is_file($file) ? json_decode(file_get_contents($file), true) : null;
        // http-ресурс
        } else {
            $this->curl->addQuery($this->config['url'] . $path, [], function($data) use (&$response, $path) {
                $response = $data;
                $spend = \Debug\Timer::stop('data-store');
                \App::logger()->info('End data-store request ' . $path . ' in ' . $spend, ['data-store']);
            }, function($e) use ($path) {
                $spend = \Debug\Timer::stop('data-store');
                \App::exception()->remove($e);
                \App::logger()->info('Fail data-store request ' . $path . ' in ' . $spend . ' with ' . $e, ['data-store']);
            }, $this->config['timeout']);
            $this->curl->execute($this->config['retryTimeout']['default'], $this->config['retryCount']);
        }

        return $response;
    }

    /**
     * @param $file
     * @param array         $data
     * @param callback      $successCallback
     * @param callback|null $failCallback
     * @param float|null    $timeout
     * @return bool
     */
    public function addQuery($file, $data = [], $successCallback, $failCallback = null, $timeout = null) {
        \Debug\Timer::start('data-store');

        if (null === $timeout) {
            $timeout = $this->config['timeout'];
        }
        if (null === $failCallback) {
            $failCallback = function(\Exception $e) {
                \App::exception()->remove($e);
            };
        }

        $result = $this->curl->addQuery($this->config['url'] . $file, $data, $successCallback, $failCallback, $timeout);

        \Debug\Timer::stop('data-store');

        return $result;
    }

    /**
     * @param int $retryTimeout
     * @param int $retryCount
     * @return void
     */
    public function execute($retryTimeout = null, $retryCount = null) {
        \Debug\Timer::start('data-store');

        if (null === $retryTimeout) {
            $retryTimeout = isset($this->config['retryTimeout']['default']) ? $this->config['retryTimeout']['default'] : 0;
        }
        if (null === $retryCount) {
            $retryCount = $this->config['retryCount'];
        }

        $this->curl->execute($retryTimeout, $retryCount);

        \Debug\Timer::stop('data-store');
    }
}