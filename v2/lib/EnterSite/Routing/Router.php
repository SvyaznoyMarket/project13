<?php

namespace EnterSite\Routing;

use Enter\Routing\Route;

class Router extends Route {
    public function __construct() {
        $this->action = ['Router', 'execute'];
        $this->parameters = [];
    }
}