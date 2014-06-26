<?php

namespace EnterSite\Repository;

use EnterSite\ConfigTrait;
use EnterSite\Model;

class DirectCredit {
    use ConfigTrait;

    /**
     * @param $categoryToken
     * @return string
     */
    public function getTypeByCategoryToken($categoryToken) {
        return in_array($categoryToken,
            ['electronics', 'sport', 'appliances', 'do_it_yourself', 'furniture', 'household', 'jewel']
        ) ? $categoryToken : 'another';
    }

    /**
     * @param Model\Product $product
     * @return bool
     */
    public function isEnabledForProduct(Model\Product $product) {
        // FIXME: использовать cartProduct.sum
        $config = $this->getConfig();

        return $config->directCredit->enabled && $product->isBuyable && ($product->price >= $config->directCredit->minPrice);
    }

    /**
     * @param Model\Cart $cart
     * @return bool
     */
    public function isEnabledForCart(Model\Cart $cart) {
        $config = $this->getConfig();

        return $config->directCredit->enabled && ($cart->sum >= $config->directCredit->minPrice);
    }
}