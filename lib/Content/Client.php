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
        $this->config = array_merge(array(
            'url' => null,
        ), $config);

        $this->curl = $curl;
    }

    /**
     * @param string $action
     * @param array  $data
     * @return array|null
     * @throws \Exception
     */
    public function query ($action, array $data = []) {
        \Debug\Timer::start('content');
        \App::logger()->info('Start content request ' . $action);

        $url = $this->config['url'] . $action . '?json=1';
        $response = null;
        try {
            $response = $this->curl->query($url, $data);
            $spend = \Debug\Timer::stop('content');
            \App::logger()->info('End content request ' . $action . ' in ' . $spend);
        } catch (\Exception $e) {
            $spend = \Debug\Timer::stop('content');
            \App::logger()->info('Fail content request ' . $action . ' in ' . $spend . ' with ' . $e);
        }

        \Util\RequestLogger::getInstance()->addLog($url, [], $spend, 'unknown');

        $data = json_decode($response, true);
        if ($error = json_last_error()) {
            throw new \Exception('Json error', $error);
        }

        return $data;
    }
}