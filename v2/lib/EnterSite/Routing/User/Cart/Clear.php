<?php

namespace EnterSite\Routing\User\Cart;

use Enter\Routing\Route;

class Clear extends Route {
    public function __construct() {
        $this->action = ['User\\Cart\Clear', 'execute'];
        $this->parameters = [];
    }
}