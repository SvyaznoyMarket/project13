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
        $ratingRepository = new Repository\Partial\Rating();

        $page->dataModule = 'product.catalog';

        foreach ($request->products as $productModel) {
            $productCard = $productCardRepository->getObject($productModel, $cartProductButtonRepository->getObject($productModel));
            // рейтинг товара
            if ($productModel->rating) {
                $rating = new Partial\Rating();
                $rating->reviewCount = $productModel->rating->reviewCount;
                $rating->stars = $ratingRepository->getStarList($productModel->rating->starScore);

                $productCard->rating = $rating;
            }

            $page->content->productBlock->products[] = $productCard;
        }

        //die(json_encode($page, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }
}