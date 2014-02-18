<?php

namespace EnterSite\Routing\ProductCard;

use Enter\Routing\Route;

class Get extends Route {
    /**
     * @param string $productPath
     */
    public function __construct($productPath) {
        $this->action = ['ProductCard', 'execute'];
        $this->parameters = [
            'productPath' => $productPath,
        ];
    }
}