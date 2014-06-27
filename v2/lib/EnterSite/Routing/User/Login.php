<?php

namespace EnterSite\Routing\User;

use EnterSite\Routing\Route;

class Login extends Route {
    public function __construct() {
        $this->action = ['User\\Login', 'execute'];
        $this->parameters = [];
    }
}