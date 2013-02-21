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

    /**
     * @param string $path
     * @return array|null
     * @throws \Exception
     */
    public function query($path) {
        \Debug\Timer::start('data-store');
        \App::logger()->info('Start data-store request ' . $path);

        $response = null;
        try {
            // локальный файл
            if (0 === strpos($path, '/')) {
                $file = \App::config()->dataDir . '/data-store' . $path;
                $response = is_file($file) ? json_decode(file_get_contents($file), true) : null;
                \Util\RequestLogger::getInstance()->addLog($file, [], 0, 'unknown');
            // http-ресурс
            } else {
                $response = $this->curl->query($this->config['url'] . $path, [], $this->config['timeout']);
            }
            $spend = \Debug\Timer::stop('data-store');
            \App::logger()->info('End data-store request ' . $path . ' in ' . $spend);
        } catch (\Exception $e) {
            $spend = \Debug\Timer::stop('data-store');
            \App::exception()->remove($e);
            \App::logger()->info('Fail data-store request ' . $path . ' in ' . $spend . ' with ' . $e);
        }

        return $response;
    }

    /**
     * @param $file
     * @param callback      $successCallback
     * @param callback|null $failCallback
     * @param float|null    $timeout
     * @return bool
     */
    public function addQuery($file, $successCallback, $failCallback = null, $timeout = null) {
        \Debug\Timer::start('data-store');

        if (null === $timeout) {
            $timeout = $this->config['timeout'];
        }
        if (null === $failCallback) {
            $failCallback = function(\Exception $e) {
                \App::exception()->remove($e);
            };
        }

        $result = $this->curl->addQuery($this->config['url'] . $file, [], $successCallback, $failCallback, $timeout);

        \Debug\Timer::stop('data-store');

        return $result;
    }

    /**
     * @param int $retryTimeout
     * @param int $retryCount
     * @return void
     */
    public function execute($retryTimeout = null, $retryCount = 0) {
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