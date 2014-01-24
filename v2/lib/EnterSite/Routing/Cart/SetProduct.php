<?php

namespace EnterSite\Routing\Cart;

use Enter\Routing\Route;
use EnterSite\Model;

class SetProduct extends Route {
    public function __construct(Model\Product $product, $quantity = 1) {
        $this->action = 'Cart\\SetProduct';
        $this->url = '/cart/product-set/' . $product->id . '?' . http_build_query(['quantity' => $quantity]);
    }
}