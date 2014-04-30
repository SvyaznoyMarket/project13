<?php

namespace EnterSite\Repository\Page\Cart;

use EnterSite\Repository;
use EnterSite\Model;
use EnterSite\Model\Partial;
use EnterSite\Model\Page\Cart\Index as Page;

class Index {
    /**
     * @param Page $page
     * @param Index\Request $request
     */
    public function buildObjectByRequest(Page $page, Index\Request $request) {
        (new Repository\Page\DefaultLayout)->buildObjectByRequest($page, $request);

        $productCardRepository = new Repository\Partial\Cart\ProductCard();
        $productSpinnerRepository = new Repository\Partial\Cart\ProductSpinner();
        foreach ($request->cartProducts as $cartProduct) {
            $product = isset($request->productsById[$cartProduct->id]) ? $request->productsById[$cartProduct->id] : null;
            if (!$product) {
                // TODO: журналирование
                continue;
            }

            $productCard = $productCardRepository->getObject($cartProduct, $product, $productSpinnerRepository->getObject($product, $cartProduct, false));
            $page->content->productBlock->products[] = $productCard;
        }

        //die(json_encode($page, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }
}