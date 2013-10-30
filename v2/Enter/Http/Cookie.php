<?php

namespace Enter\Http;

class Cookie {
    /** @var string */
    public $name;
    /** @var string */
    public $value;
    /** @var string */
    public $domain;
    /** @var \DateTime */
    public $expire;
    /** @var string */
    public $path;
    /** @var boolean */
    public $secure;
    /** @var boolean */
    public $httpOnly;
}