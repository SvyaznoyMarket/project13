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
        return sprintf('%s {timeout: %s ms, delay: %s}', urldecode($this->options[CURLOPT_URL]), $this->options[CURLOPT_TIMEOUT_MS], $this->delay);
    }
}