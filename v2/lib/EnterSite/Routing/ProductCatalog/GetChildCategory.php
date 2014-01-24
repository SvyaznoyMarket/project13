<?php

namespace EnterSite\Routing\ProductCatalog;

use Enter\Routing\Route;

class GetChildCategory extends Route {
    public function __construct() {
        $this->action = 'ProductCatalog\\ChildCategory';
    }
}