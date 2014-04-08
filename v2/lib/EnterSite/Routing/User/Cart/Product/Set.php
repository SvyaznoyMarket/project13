<?php

namespace EnterSite\Routing\User\Cart\Product;

use Enter\Routing\Route;

class Set extends Route {
    public function __construct() {
        $this->action = ['User\\Cart\\SetProduct', 'execute'];
        $this->parameters = [];
    }
}