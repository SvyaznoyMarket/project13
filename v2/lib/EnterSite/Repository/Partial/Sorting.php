<?php

namespace EnterSite\Repository\Partial;

use Enter\Http;
use EnterSite\RouterTrait;
use EnterSite\UrlHelperTrait;
use EnterSite\Routing;
use EnterSite\Repository;
use EnterSite\Model;
use EnterSite\Model\Partial;

class Sorting {
    use RouterTrait, UrlHelperTrait;

    /**
     * @param Model\Product\Sorting[] $sortingModels
     * @param Model\Product\Sorting|null $currentSortingModel
     * @param Routing\Route|null $route
     * @param Http\Request|null $httpRequest
     * @return Partial\Sorting[]
     */
    public function getList(
        array $sortingModels,
        Model\Product\Sorting $currentSortingModel = null,
        Routing\Route $route = null,
        Http\Request $httpRequest = null
    ) {
        $router = $this->getRouter();
        $urlHelper = $this->getUrlHelper();

        $sortings = [];

        foreach ($sortingModels as $sortingModel) {
            $sorting = new Partial\Sorting();
            $sorting->name = $sortingModel->name;
            if ($route && $httpRequest) {
                $sorting->url = $router->getUrlByRoute($route, $urlHelper->replace($route, $httpRequest, [
                    'sort' => ('default' == $sortingModel->token) ? null : ($sortingModel->token . '-' . $sortingModel->direction),
                ]));
            }
            $sorting->isActive = $currentSortingModel && ($currentSortingModel->token == $sortingModel->token) && ($currentSortingModel->direction == $sortingModel->direction);

            $sortings[] = $sorting;
        }

        return $sortings;
    }
}