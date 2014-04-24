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
        $slider = $productSliderRepository->getObject('alsoBoughtSlider', $url);
        $slider->hasCategories = false;
        foreach ($request->alsoBoughtIdList as $productId) {
            /** @var Model\Product|null $productModel */
            $productModel = !empty($request->productsById[$productId]) ? $request->productsById[$productId] : null;
            if (!$productModel) continue;

            $slider->productCards[] = $productCardRepository->getObject($productModel, $cartProductButtonRepository->getObject($productModel));
        }
        $slider->count = count($slider->productCards);
        $page->widgets['.' . $slider->widgetId] = $slider;

        // alsoViewed slider
        $slider = $productSliderRepository->getObject('alsoViewedSlider', $url);
        $slider->hasCategories = false;
        foreach ($request->alsoViewedIdList as $productId) {
            /** @var Model\Product|null $productModel */
            $productModel = !empty($request->productsById[$productId]) ? $request->productsById[$productId] : null;
            if (!$productModel) continue;

            $slider->productCards[] = $productCardRepository->getObject($productModel, $cartProductButtonRepository->getObject($productModel));
        }
        $slider->count = count($slider->productCards);
        $page->widgets['.' . $slider->widgetId] = $slider;

        // similar slider
        $slider = $productSliderRepository->getObject('similarSlider', $url);;
        $slider->hasCategories = false;
        foreach ($request->similarIdList as $productId) {
            /** @var Model\Product|null $productModel */
            $productModel = !empty($request->productsById[$productId]) ? $request->productsById[$productId] : null;
            if (!$productModel) continue;

            $slider->productCards[] = $productCardRepository->getObject($productModel, $cartProductButtonRepository->getObject($productModel));
        }
        $slider->count = count($slider->productCards);
        $page->widgets['.' . $slider->widgetId] = $slider;
    }
}