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
        $router = $this->getRouter();
        $cartProductButtonRepository = new Repository\Partial\Cart\ProductButton();
        $productCardRepository = new Repository\Partial\ProductCard();
        $productSliderRepository = new Repository\Partial\ProductSlider();

        $url = $router->getUrlByRoute(new Routing\Product\GetRecommendedList($request->product->id));

        // alsoBought slider
        $page->alsoBoughtSlider = $productSliderRepository->getObject('alsoBoughtSlider', $url);
        $page->alsoBoughtSlider->hasCategories = false;
        foreach ($request->alsoBoughtIdList as $productId) {
            /** @var Model\Product|null $productModel */
            $productModel = !empty($request->productsById[$productId]) ? $request->productsById[$productId] : null;
            if (!$productModel) continue;

            $page->alsoBoughtSlider->productCards[] = $productCardRepository->getObject($productModel, $cartProductButtonRepository->getObject($productModel));
        }
        $page->alsoBoughtSlider->count = count($page->alsoBoughtSlider->productCards);

        // alsoViewed slider
        $page->alsoViewedSlider = $productSliderRepository->getObject('alsoViewedSlider', $url);
        $page->alsoViewedSlider->hasCategories = false;
        foreach ($request->alsoViewedIdList as $productId) {
            /** @var Model\Product|null $productModel */
            $productModel = !empty($request->productsById[$productId]) ? $request->productsById[$productId] : null;
            if (!$productModel) continue;

            $page->alsoViewedSlider->productCards[] = $productCardRepository->getObject($productModel, $cartProductButtonRepository->getObject($productModel));
        }
        $page->alsoViewedSlider->count = count($page->alsoViewedSlider->productCards);

        // similar slider
        $page->similarSlider = $productSliderRepository->getObject('similarSlider', $url);;
        $page->similarSlider->hasCategories = false;
        foreach ($request->similarIdList as $productId) {
            /** @var Model\Product|null $productModel */
            $productModel = !empty($request->productsById[$productId]) ? $request->productsById[$productId] : null;
            if (!$productModel) continue;

            $page->similarSlider->productCards[] = $productCardRepository->getObject($productModel, $cartProductButtonRepository->getObject($productModel));
        }
        $page->similarSlider->count = count($page->similarSlider->productCards);
    }
}