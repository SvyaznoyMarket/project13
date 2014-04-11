<?php

namespace EnterSite\Repository\Page\Product;

use EnterSite\ConfigTrait;
use EnterSite\LoggerTrait;
use EnterSite\RouterTrait;
use EnterSite\DateHelperTrait;
use EnterSite\TranslateHelperTrait;
use EnterSite\Routing;
use EnterSite\Repository;
use EnterSite\Model;
use EnterSite\Model\Partial;
use EnterSite\Model\Page\Product\RecommendedList as Page;

class RecommendedList {
    use ConfigTrait, LoggerTrait, RouterTrait, DateHelperTrait, TranslateHelperTrait {
        ConfigTrait::getConfig insteadof LoggerTrait;
    }

    /**
     * @param Page $page
     * @param RecommendedList\Request $request
     */
    public function buildObjectByRequest(Page $page, RecommendedList\Request $request) {
        $cartProductButtonRepository = new Repository\Partial\Cart\ProductButton();
        $productCardRepository = new Repository\Partial\ProductCard();

        // alsoBought slider
        $page->alsoBoughtSlider = new Partial\ProductSlider();
        $page->alsoBoughtSlider->hasCategories = false;
        foreach ($request->alsoBoughtIdList as $productId) {
            /** @var Model\Product|null $productModel */
            $productModel = !empty($request->productsById[$productId]) ? $request->productsById[$productId] : null;
            if (!$productModel) continue;

            $page->alsoBoughtSlider->productCards[] = $productCardRepository->getObject($productModel, $cartProductButtonRepository->getObject($productModel));
        }

        // alsoViewed slider
        $page->alsoViewedSlider = new Partial\ProductSlider();
        $page->alsoViewedSlider->hasCategories = false;
        foreach ($request->alsoViewedIdList as $productId) {
            /** @var Model\Product|null $productModel */
            $productModel = !empty($request->productsById[$productId]) ? $request->productsById[$productId] : null;
            if (!$productModel) continue;

            $page->alsoViewedSlider->productCards[] = $productCardRepository->getObject($productModel, $cartProductButtonRepository->getObject($productModel));
        }

        // similar slider
        $page->similarSlider = new Partial\ProductSlider();
        $page->similarSlider->hasCategories = false;
        foreach ($request->similarIdList as $productId) {
            /** @var Model\Product|null $productModel */
            $productModel = !empty($request->productsById[$productId]) ? $request->productsById[$productId] : null;
            if (!$productModel) continue;

            $page->similarSlider->productCards[] = $productCardRepository->getObject($productModel, $cartProductButtonRepository->getObject($productModel));
        }
    }
}