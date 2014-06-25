<?php

namespace EnterSite\Routing\User\Cart\Product; // FIXME: изменить namespace на EnterSite\Routing\Cart

use EnterSite\Routing\Route;

class Set extends Route {
    public function __construct() {
        $this->action = ['User\\Cart\\SetProduct', 'execute'];
        $this->parameters = [];
    }
}