<?php

namespace EnterLab\Curl;

class Request {
    /**
     * Задержка перед выполнением, мс
     *
     * @var int
     */
    public $delay = 0;
    /**
     * CURLOPT_*
     *
     * @var array
     */
    public $options = [
        CURLOPT_URL        => null,
        CURLOPT_TIMEOUT_MS => null,
    ];

    public function __toString() {
        return sprintf('%s {delay: %s, timeout: %s}', urldecode($this->options[CURLOPT_URL]), $this->delay, $this->options[CURLOPT_TIMEOUT_MS]);
    }
}