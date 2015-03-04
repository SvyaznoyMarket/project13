<?php

namespace EnterLab\Curl;

class Response
{
    /** @var int */
    public $statusCode;
    /**
     * Заголовки ответа
     *
     * @var string[]
     */
    public $headers = [];
    /**
     * Тело ответа
     *
     * @var string|null
     */
    public $body;
    /**
     * Результат curl_getinfo
     *
     * @var array
     */
    public $info = [];
    /** @var \Exception|null */
    public $error;
}