<?php

namespace EnterSite\Repository\Partial\Cart;

use EnterSite\Repository;
use EnterSite\Model;
use EnterSite\Model\Partial;

class ProductSpinner {
    /**
     * @param Model\Product $product
     * @param Model\Cart\Product|null $cartProduct
     * @return Partial\Cart\ProductSpinner
     */
    public function getObject(
        Model\Product $product,
        Model\Cart\Product $cartProduct = null
    ) {
        $spinner = new Partial\Cart\ProductSpinner();

        $spinner->id = self::getId($product->id);
        $spinner->targetId = Repository\Partial\Cart\ProductButton::getId($product->id);

        if ($cartProduct) {
            $spinner->class = ' mDisabled';
        }

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