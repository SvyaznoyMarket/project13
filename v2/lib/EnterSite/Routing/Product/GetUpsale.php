<?php

namespace EnterSite\Routing\Product;

use Enter\Routing\Route;

class GetUpsale extends Route {
    /**
     * @param string $productId
     */
    public function __construct($productId) {
        $this->action = ['Product\\Upsale', 'execute'];
        $this->parameters = [
            'productId' => $productId,
        ];
    }
}