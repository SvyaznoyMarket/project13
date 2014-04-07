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

        $spinner->id = self::getId($product->id);

        return $spinner;
    }

    /**
     * @param $productId
     * @return string
     */
    public static function getId($productId) {
        return 'id-cart-product-buySpinner-' . $productId;
    }
}