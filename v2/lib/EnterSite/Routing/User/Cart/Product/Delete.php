<?php

namespace EnterSite\Routing\User\Cart\Product; // FIXME: изменить namespace на EnterSite\Routing\Cart

use Enter\Routing\Route;

class Delete extends Route {
    public function __construct() {
        $this->action = ['User\\Cart\\DeleteProduct', 'execute'];
        $this->parameters = [];
    }
}