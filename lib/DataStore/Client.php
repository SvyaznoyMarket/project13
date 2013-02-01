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
            'url' => null,
        ], $config);

        $this->curl = $curl;
    }

    /**
     * @param string $file
     * @return array|null
     * @throws \Exception
     */
    public function query($file) {
        \Debug\Timer::start('data-store');
        \App::logger()->info('Start data-store request ' . $file);

        $url = $this->config['url'] . $file;
        $response = null;
        try {
            // локальный файл
            if (0 === strpos($url, '/')) {
                $response = file_get_contents($url);
            // http-ресурс
            } else {
                $response = $this->curl->query($url);
            }
            $spend = \Debug\Timer::stop('data-store');
            \App::logger()->info('End data-store request ' . $file . ' in ' . $spend);
        } catch (\Exception $e) {
            $spend = \Debug\Timer::stop('content');
            \App::logger()->info('Fail data-store request ' . $file . ' in ' . $spend . ' with ' . $e);
        }

        \Util\RequestLogger::getInstance()->addLog($url, [], $spend, 'unknown');

        if ('json' == pathinfo($file, PATHINFO_EXTENSION)) {
            $data = json_decode($response, true);
            if ($error = json_last_error()) {
                throw new \Exception('Json error', $error);
            }
        }

        return $data;
    }
}