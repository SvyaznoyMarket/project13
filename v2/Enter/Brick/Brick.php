<?php

namespace Enter\Brick;

class Brick {
    /** @var object */
    public $request;

    /** @var object */
    public $response;

    /** @var object */
    public $config;

    /** @var callable */
    public $controller;

    /** @var \ReflectionParameter[] */
    public $requestParameters = [];
}