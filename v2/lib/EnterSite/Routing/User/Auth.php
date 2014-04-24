<?php

namespace EnterSite\Routing\User;

use Enter\Routing\Route;

class Auth extends Route {
    public function __construct() {
        $this->action = ['User\\Auth', 'execute'];
        $this->parameters = [];
    }
}