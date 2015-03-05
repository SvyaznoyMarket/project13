<?php

namespace EnterLab\Curl;

class Config
{
    /**
     * Задержка для curl_multi_select, мс
     *
     * @var int
     */
    public $selectTimeout = 500;
    /**
     * Задержка для curl_multi_timeout, мкс
     *
     * @var int
     */
    public $multiSelectTimeout = 1000;
    /**
     * @var int
     */
    public $handleLimit = 100;
    /**
     * Таймаут для запросов по умолчанию, мс
     *
     * @var int
     */
    public $defaultQueryTimeout;
}