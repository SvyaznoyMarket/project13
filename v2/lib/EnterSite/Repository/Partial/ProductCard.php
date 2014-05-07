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
        $card->shownPrice = $product->price ? number_format((float)$product->price, 0, ',', ' ') : null;
        $card->oldPrice = $product->oldPrice;
        $card->shownOldPrice = $product->oldPrice ? number_format((float)$product->oldPrice, 0, ',', ' ') : null;
        if ($photo = reset($product->media->photos)) {
            /** @var Model\Product\Media\Photo $photo */
            $card->image = (string)(new Routing\Product\Media\GetPhoto($photo->source, $photo->id, 2));
        }
        $card->id = $product->id;
        $card->categoryId = $product->category ? $product->category->id : null;
        $card->cartButton = $cartButton;

        return $card;
    }
}