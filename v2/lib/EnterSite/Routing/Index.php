<?php

namespace EnterSite\Routing;

use Enter\Routing\Route;

class Index extends Route {
    public function __construct() {
        $this->action = ['Index', 'execute'];
        $this->parameters = [];
    }
}