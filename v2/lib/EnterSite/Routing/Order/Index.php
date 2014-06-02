<?php

namespace EnterSite\Routing\Order;

use EnterSite\Routing\Route;

class Index extends Route {
    public function __construct() {
        $this->action = ['Order\\Index', 'execute'];
        $this->parameters = [];
    }
}