<?php

namespace Enter\Curl;

class Config {
    /** @var string */
    public $encoding;
    /** @var string */
    public $referer;
    /** @var array */
    public $httpheader = [];
    /** @var float */
    public $timeout;
    /** @var int */
    public $retryCount;
    /** @var float */
    public $retryTimeout;
}