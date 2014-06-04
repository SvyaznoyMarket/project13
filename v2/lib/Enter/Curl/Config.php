<?php

namespace Enter\Curl;

class Config {
    /** @var string */
    public $encoding;
    /** @var string */
    public $referer;
    /** @var array */
    public $httpheader = [];
    /** @var int */
    public $retryCount;
    /** @var float */
    public $retryTimeout;
}