<?php

namespace EnterSite\Routing\Product;

use Enter\Routing\Route;
use EnterSite\Model\Product;

class Upsale extends Route {
    /**
     * @param Product $product
     */
    public function __construct(Product $product) {
        $this->action = 'Product\\Upsale';
        $this->url = '/ajax/upsale/' . $product->id;
    }
}