<?php

namespace EnterSite\Routing\User\Cart\Product;

use Enter\Routing\Route;

class Delete extends Route {
    public function __construct() {
        $this->action = ['User\\Cart\\DeleteProduct', 'execute'];
        $this->parameters = [];
    }
}