<?php

namespace EnterSite\Repository\Partial;

use EnterSite\Routing;
use EnterSite\Model;
use EnterSite\Model\Partial;

class ProductCard {
    /**
     * @param Model\Product $product
     * @param Partial\Cart\ProductButton|null $cartButton
     * @return Partial\ProductCard
     */
    public function getObject(
        Model\Product $product,
        Partial\Cart\ProductButton $cartButton = null
    ) {
        $card = new Partial\ProductCard();

        $card->name = $product->name;
        $card->url = $product->link;
        $card->price = $product->price;
        $card->shownPrice = number_format((float)$product->price, 0, ',', ' ');
        if ($photo = reset($product->media->photos)) {
            /** @var Model\Product\Media\Photo $photo */
            $card->image = new Routing\Product\Media\GetPhoto($photo->source, $photo->id, 1);
        }
        $card->cartButton = $cartButton;

        return $card;
    }
}