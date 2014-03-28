<?php

namespace EnterSite\Repository\Partial\Cart;

use EnterSite\Repository;
use EnterSite\Model;
use EnterSite\Model\Partial;

class ProductSpinner {
    /**
     * @param Model\Product $product
     * @return Partial\Cart\ProductSpinner
     */
    public function getObject(
        Model\Product $product
    ) {
        $spinner = new Partial\Cart\ProductSpinner();

        $spinner->dataTarget = Repository\Partial\Cart\ProductButton::getId($product->id);

        return $spinner;
    }
}