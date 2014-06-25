<?php

namespace Enter\Routing;

class Route {
    /** @var callable|null */
    public $action;
    /** @var array */
    public $parameters = [];
}