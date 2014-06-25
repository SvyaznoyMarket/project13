<?php

namespace EnterSite\Routing\User;

use EnterSite\Routing\Route;

class Get extends Route {
    public function __construct() {
        $this->action = ['User\\Get', 'execute'];
        $this->parameters = [];
    }
}