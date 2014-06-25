<?php

namespace EnterSite\Routing\Cart;

use EnterSite\Routing\Route;

class DeleteProduct extends Route {
    /**
     * @param string $productId
     */
    public function __construct($productId) {
        $this->action = ['Cart\\DeleteProduct', 'execute'];
        $this->parameters = [
            'productId' => $productId,
        ];
    }
}