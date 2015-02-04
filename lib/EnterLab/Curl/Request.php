<?php

namespace EnterLab\Curl;

class Request {
    /**
     * Задержка перед выполнением, мс
     *
     * @var int
     */
    public $delay;
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
        return sprintf('%s (%s ms)', $this->options[CURLOPT_URL], $this->options[CURLOPT_TIMEOUT_MS]);
    }
}