<?php

namespace EnterSite\Routing\User;

use EnterSite\Routing\Route;

class Index extends Route {
    public function __construct() {
        $this->action = ['User\\Index', 'execute'];
        $this->parameters = [];
    }
}