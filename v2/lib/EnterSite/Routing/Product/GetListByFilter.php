<?php

namespace EnterSite\Routing\Product;

use Enter\Routing\Route;

class GetListByFilter extends Route {
    public function __construct() {
        $this->action = ['Product\\GetListByFilter', 'execute'];
    }
}