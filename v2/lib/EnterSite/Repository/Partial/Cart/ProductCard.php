<?php

namespace EnterSite\Repository\Partial\Cart;

use EnterSite\Routing;
use EnterSite\Model;
use EnterSite\Model\Partial;
use EnterSite\Repository;

class ProductCard {
    /**
     * @param Model\Cart\Product $cartProduct
     * @param Model\Product $product
     * @param Partial\Cart\ProductSpinner|null $cartSpinner
     * @param Partial\Cart\ProductDeleteButton|null $cartDeleteButton
     * @return Partial\ProductCard
     */
    public function getObject(
        Model\Cart\Product $cartProduct,
        Model\Product $product,
        Partial\Cart\ProductSpinner $cartSpinner = null,
        Partial\Cart\ProductDeleteButton $cartDeleteButton = null
    ) {
        $card = new Partial\Cart\ProductCard();

        $card->name = $product->name;
        $card->url = $product->link;
        $card->price = $product->price;
        $card->shownPrice = $product->price ? number_format((float)$product->price, 0, ',', ' ') : null;
        $card->sum = (new Repository\Partial\Cart\ProductSum())->getObject($cartProduct);
        $card->oldPrice = $product->oldPrice;
        $card->shownOldPrice = $product->oldPrice ? number_format((float)$product->oldPrice, 0, ',', ' ') : null;
        if ($photo = reset($product->media->photos)) {
            /** @var Model\Product\Media\Photo $photo */
            $card->image = (string)(new Routing\Product\Media\GetPhoto($photo->source, $photo->id, 1));
        }
        $card->id = $product->id;
        $card->cartSpinner = $cartSpinner;
        $card->cartDeleteButton = $cartDeleteButton;

        return $card;
    }
}