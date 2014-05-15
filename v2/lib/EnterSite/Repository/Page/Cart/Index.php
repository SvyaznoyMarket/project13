<?php

namespace EnterSite\Repository\Page\Cart;

use EnterSite\TranslateHelperTrait;
use EnterSite\Repository;
use EnterSite\Model;
use EnterSite\Model\Partial;
use EnterSite\Model\Page\Cart\Index as Page;

class Index {
    use TranslateHelperTrait;

    /**
     * @param Page $page
     * @param Index\Request $request
     */
    public function buildObjectByRequest(Page $page, Index\Request $request) {
        (new Repository\Page\DefaultLayout)->buildObjectByRequest($page, $request);

        $productCardRepository = new Repository\Partial\Cart\ProductCard();
        $productSpinnerRepository = new Repository\Partial\Cart\ProductSpinner();
        $productDeleteButtonRepository = new Repository\Partial\Cart\ProductDeleteButton();

        // body[data-module]
        $page->dataModule = 'cart';

        if (count($request->cart)) {
            $page->content->cart->sum = $request->cart->sum;
            $page->content->cart->shownSum = number_format((float)$request->cart->sum, 0, ',', ' ');
            $page->content->cart->quantity = count($request->cart);
            $page->content->cart->shownQuantity = $page->content->cart->quantity . ' ' . $this->getTranslateHelper()->numberChoice($page->content->cart->quantity, ['товар', 'товара', 'товаров']);
        } else {
            $page->content->cart = false;
        }

        foreach (array_reverse($request->cartProducts) as $cartProduct) {
            $product = isset($request->productsById[$cartProduct->id]) ? $request->productsById[$cartProduct->id] : null;
            if (!$product) {
                // TODO: журналирование
                continue;
            }

            $productCard = $productCardRepository->getObject(
                $cartProduct,
                $product,
                $productSpinnerRepository->getObject($product, $cartProduct->quantity, false, false),
                $productDeleteButtonRepository->getObject($product)
            );
            $page->content->productBlock->products[] = $productCard;
        }

        //die(json_encode($page, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }
}