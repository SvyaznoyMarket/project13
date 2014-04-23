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
        $spinner->widgetId = self::getWidgetId($product->id);
        $spinner->buttonId = Repository\Partial\Cart\ProductButton::getId($product->id);
        $spinner->value = 1;

        if ($cartProduct) {
            $spinner->class = ' mDisabled';
            $spinner->value = $cartProduct->quantity;
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

    /**
     * @param $productId
     * @return string
     */
    public static function getWidgetId($productId) {
        return self::getId($productId) . '-widget';
    }
}