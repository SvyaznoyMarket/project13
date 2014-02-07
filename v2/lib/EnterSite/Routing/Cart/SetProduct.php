<?php

namespace EnterSite\Routing\Cart;

use Enter\Routing\Route;

class SetProduct extends Route {
    /**
     * @param string $productId
     * @param int $quantity
     */
    public function __construct($productId, $quantity = 1) {
        $this->action = ['Cart\\SetProduct', 'execute'];
        $this->parameters = [
            'productId' => $productId,
        ];
    }
}