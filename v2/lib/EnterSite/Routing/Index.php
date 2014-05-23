<?php

namespace EnterSite\Routing;

use EnterSite\Routing\Route;

class Index extends Route {
    public function __construct() {
        $this->action = ['Index', 'execute'];
        $this->parameters = [];
    }
}