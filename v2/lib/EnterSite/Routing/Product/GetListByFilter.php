<?php

namespace EnterSite\Routing\Product;

use EnterSite\Routing\Route;

class GetListByFilter extends Route {
    public function __construct() {
        $this->action = ['Product\\ListByFilter', 'execute'];
    }
}