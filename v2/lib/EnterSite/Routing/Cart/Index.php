<?php

namespace EnterSite\Routing\Cart;

use EnterSite\Routing\Route;

class Index extends Route {
    public function __construct() {
        $this->action = ['Cart\\Index', 'execute'];
        $this->parameters = [];
    }
}