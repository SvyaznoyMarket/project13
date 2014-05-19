<?php

namespace EnterSite\Repository\Partial\Cart;

use EnterSite\Model;
use EnterSite\Model\Partial;

class ProductSum {
    /**
     * @param Model\Cart\Product|null $cartProduct
     * @return Partial\Cart\ProductSum|null
     */
    public function getObject(
        Model\Cart\Product $cartProduct
    ) {
        $productSum = null;

        if ($cartProduct) {
            $productSum = new Partial\Cart\ProductSum();
            $productSum->widgetId = self::getWidgetId($cartProduct->id);
            $productSum->value = $cartProduct->sum;
            $productSum->shownValue = number_format((float)$cartProduct->sum, 0, ',', ' ');
        }

        return $productSum;
    }

    /**
     * @param $productId
     * @return string
     */
    public static function getId($productId) {
        return 'id-cart-productSum-' . $productId;
    }

    /**
     * @param $productId
     * @return string
     */
    public static function getWidgetId($productId) {
        return self::getId($productId) . '-widget';
    }
}