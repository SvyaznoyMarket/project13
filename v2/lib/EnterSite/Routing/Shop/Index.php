<?php

namespace EnterSite\Routing\Shop;

use EnterSite\Routing\Route;

class Index extends Route {
    public function __construct() {
        $this->action = ['Shop\\Index', 'execute'];
        $this->parameters = [];
    }
}