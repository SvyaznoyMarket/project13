<?php

namespace EnterSite\Routing\User;

use Enter\Routing\Route;

class Get extends Route {
    public function __construct() {
        $this->action = ['User\\Get', 'execute'];
        $this->parameters = [];
    }
}