<?php

namespace EnterSite\Repository\Partial;

use EnterSite\Model;
use EnterSite\Model\Partial;

class ProductCard {
    /**
     * @param Model\Product $product
     * @param Partial\Cart\ProductButton $cartButton
     * @return Partial\ProductCard
     */
    public function getObject(
        Model\Product $product,
        Partial\Cart\ProductButton $cartButton
    ) {
        $card = new Partial\ProductCard();

        $card->name = $product->name;
        $card->url = $product->link;
        $card->cartButton = $cartButton;

        return $card;
    }
}