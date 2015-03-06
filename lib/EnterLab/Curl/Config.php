<?php

namespace EnterLab\Curl;

class Config
{
    /**
     * Задержка для curl_multi_select, мс
     *
     * @var int
     */
    public $selectTimeout = 50;
    /**
     * Задержка для curl_multi_timeout, мкс
     *
     * @var int
     */
    public $multiSelectTimeout = 250;
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