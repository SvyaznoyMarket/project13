<?php

namespace EnterSite\Repository\Page\ProductCatalog;

use EnterSite\Repository;
use EnterSite\Model;
use EnterSite\Model\Partial;
use EnterSite\Model\Page\ProductCatalog\ChildCategory as Page;

class ChildCategory {
    /**
     * @param Page $page
     * @param ChildCategory\Request $request
     */
    public function buildObjectByRequest(Page $page, ChildCategory\Request $request) {
        (new Repository\Page\DefaultLayout)->buildObjectByRequest($page, $request);

        $productCardRepository = new Repository\Partial\ProductCard();
        $cartProductButtonRepository = new Repository\Partial\Cart\ProductButton();
        foreach ($request->products as $product) {
            $productCard = $productCardRepository->getObject($product, $cartProductButtonRepository->getObject($product));
            $page->content->productBlock->products[] = $productCard;
        }

        //die(json_encode($page, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }
}