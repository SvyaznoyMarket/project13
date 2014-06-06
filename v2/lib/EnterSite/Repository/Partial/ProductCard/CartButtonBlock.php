<?php

namespace EnterSite\Repository\Partial\ProductCard;

use EnterSite\Routing;
use EnterSite\Repository;
use EnterSite\Model;
use EnterSite\Model\Partial;

class CartButtonBlock {

    public function getObject(
        Model\Product $product,
        Model\Cart\Product $cartProduct = null
    ) {
        $block = new Model\Partial\ProductCard\CartButtonBlock();
        $block->widgetId = self::getWidgetId($product->id);

        $block->cartLink = (new Repository\Partial\Cart\ProductLink())->getObject($product, $cartProduct) ?: false;
        if (!$cartProduct) {
            $block->cartButton = (new Repository\Partial\Cart\ProductButton())->getObject($product);
            $block->cartSpinner = (new Repository\Partial\Cart\ProductSpinner())->getObject($product);
            $block->cartQuickButton = (new Repository\Partial\Cart\ProductQuickButton())->getObject($product);
        }

        return $block;
    }

    /**
     * @param $productId
     * @return string
     */
    public static function getId($productId) {
        return 'id-productButtonBlock-' . $productId;
    }

    /**
     * @param $productId
     * @return string
     */
    public static function getWidgetId($productId) {
        return self::getId($productId) . '-widget';
    }
}