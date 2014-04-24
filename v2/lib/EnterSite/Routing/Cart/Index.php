<?php

namespace EnterSite\Routing\Cart;

use Enter\Routing\Route;

class Index extends Route {
    public function __construct() {
        $this->action = ['Cart\\Index', 'execute'];
        $this->parameters = [];
    }
}