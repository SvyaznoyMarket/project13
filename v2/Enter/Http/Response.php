<?php

namespace Enter\Http;

class Response {
    const STATUS_OK = 200;
    const STATUS_MOVED_PERMANENTLY = 301;
    const STATUS_BAD_REQUEST = 400;
    const STATUS_NOT_FOUND = 404;
    const STATUS_INTERNAL_SERVER_ERROR = 500;

    public $content = [];

    public function __construct($content = '', $statusCode = self::STATUS_OK) {
        $this->content = $content;
    }
}