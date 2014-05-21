<?php

namespace EnterSite\Routing\Product;

use Enter\Routing\Route;

class GetListByFilter extends Route {
    /**
     * @param string $productId
     */
    public function __construct($productId) {
        $this->action = ['Product\\GetListByFilter', 'execute'];
    }
}