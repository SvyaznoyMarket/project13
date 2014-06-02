<?php

namespace EnterSite\Routing;

use EnterSite\Routing\Route;

class Router extends Route {
    public function __construct() {
        $this->action = ['Router', 'execute'];
        $this->parameters = [];
    }
}