<?php

namespace EnterSite\Repository\Partial;

use Enter\Http;
use EnterSite\RouterTrait;
use EnterSite\UrlHelperTrait;
use EnterSite\ViewHelperTrait;
use EnterSite\Routing;
use EnterSite\Repository;
use EnterSite\Model;
use EnterSite\Model\Partial;

class ProductSortingBlock {
    use RouterTrait, UrlHelperTrait, ViewHelperTrait;

    /**
     * @param Model\Product\Sorting[] $sortingModels
     * @param Model\Product\Sorting|null $currentSortingModel
     * @param Routing\Route|null $route
     * @param Http\Request|null $httpRequest
     * @return \EnterSite\Model\Partial\SortingBlock
     */
    public function getObject(
        array $sortingModels,
        Model\Product\Sorting $currentSortingModel = null,
        Routing\Route $route = null,
        Http\Request $httpRequest = null
    ) {
        $router = $this->getRouter();
        $urlHelper = $this->getUrlHelper();
        $viewHelper = $this->getViewHelper();

        $block = new Partial\SortingBlock();
        $block->widgetId = 'id-productSorting';

        foreach ($sortingModels as $sortingModel) {
            $urlParams = [
                'sort' => ('default' == $sortingModel->token) ? null : ($sortingModel->token . '-' . $sortingModel->direction),
            ];

            $sorting = new Partial\SortingBlock\Sorting();
            $sorting->name = $sortingModel->name;
            $sorting->dataValue = $viewHelper->json($urlParams);
            if ($route && $httpRequest) {
                $sorting->url = $router->getUrlByRoute($route, $urlHelper->replace($route, $httpRequest, $urlParams));
            }

            if ($currentSortingModel && ($currentSortingModel->token == $sortingModel->token) && ($currentSortingModel->direction == $sortingModel->direction)) {
                $block->sorting = $sorting;
                $sorting->isActive = true;
            } else {
                $sorting->isActive = false;
            }

            $block->sortings[] = $sorting;
        }

        return $block;
    }
}