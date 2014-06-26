<?php

namespace EnterSite\Controller\ProductCatalog;

use Enter\Http;
use EnterSite\ConfigTrait;
use EnterSite\CurlClientTrait;
use EnterSite\MustacheRendererTrait;
use EnterSite\DebugContainerTrait;
use EnterSite\Controller;
use EnterSite\Repository;
use EnterSite\Curl\Query;
use EnterSite\Model;
use EnterSite\Model\Page\ProductCatalog\ChildCategory as Page;

class ChildCategory {
    use ConfigTrait, CurlClientTrait, MustacheRendererTrait, DebugContainerTrait {
        ConfigTrait::getConfig insteadof CurlClientTrait, MustacheRendererTrait, DebugContainerTrait;
    }

    /**
     * @param Http\Request $request
     * @return Http\Response
     */
    public function execute(Http\Request $request) {
        $config = $this->getConfig();
        $curl = $this->getCurlClient();
        $productRepository = new Repository\Product();
        $productCategoryRepository = new Repository\Product\Category();
        $filterRepository = new Repository\Product\Filter();

        // ид региона
        $regionId = (new Repository\Region())->getIdByHttpRequestCookie($request);

        // токен категории
        $categoryToken = $productCategoryRepository->getTokenByHttpRequest($request);

        // номер страницы
        $pageNum = (new Repository\PageNum())->getByHttpRequest($request);
        $limit = (new Repository\Product\Catalog\Config())->getLimitByHttpRequest($request);

        // список сортировок
        $sortings = (new Repository\Product\Sorting())->getObjectList();

        // сортировка
        $sorting = (new Repository\Product\Sorting())->getObjectByHttpRequest($request);
        if (!$sorting) {
            $sorting = reset($sortings);
        }

        // запрос региона
        $regionQuery = new Query\Region\GetItemById($regionId);
        $curl->prepare($regionQuery);

        $curl->execute();

        // регион
        $region = (new Repository\Region())->getObjectByQuery($regionQuery);

        // запрос категории
        $categoryItemQuery = new Query\Product\Category\GetItemByToken($categoryToken, $region->id);
        $curl->prepare($categoryItemQuery);

        $categoryAdminItemQuery = null;
        if ($config->adminService->enabled) {
            $categoryAdminItemQuery = new Query\Product\Category\GetAdminItemByToken($categoryToken, $region->id);
            $curl->prepare($categoryAdminItemQuery);
        }

        $curl->execute();

        // категория
        $category = $productCategoryRepository->getObjectByQuery($categoryItemQuery, $categoryAdminItemQuery);
        if (!$category) {
            // костыль для ядра
            $categoryUi = isset($categoryAdminItemQuery->getResult()['ui']) ? $categoryAdminItemQuery->getResult()['ui'] : null;
            $categoryItemQuery = $categoryUi ? new Query\Product\Category\GetItemByUi($categoryUi, $region->id) : null;

            if ($categoryItemQuery) {
                $curl->prepare($categoryItemQuery)->execute();
                $category = $productCategoryRepository->getObjectByQuery($categoryItemQuery, $categoryAdminItemQuery);
            }
        }

        if (!$category) {
            return (new Controller\Error\NotFound())->execute($request, sprintf('Категория товара @%s не найдена', $categoryToken));
        }
        if ($category->redirectLink) {
            return (new Controller\Redirect())->execute($category->redirectLink . ((bool)$request->getQueryString() ? ('?' . $request->getQueryString()) : ''), Http\Response::STATUS_MOVED_PERMANENTLY);
        }

        // фильтры в http-запросе
        $requestFilters = $filterRepository->getRequestObjectListByHttpRequest($request);
        // фильтр категории в http-запросе
        $requestFilters[] = $filterRepository->getRequestObjectByCategory($category);

        // запрос фильтров
        $filterListQuery = new Query\Product\Filter\GetListByCategoryId($category->id, $region->id);
        $curl->prepare($filterListQuery);

        // запрос предка категории
        $branchCategoryItemQuery = new Query\Product\Category\GetBranchItemByCategoryObject($category, $region->id);
        $curl->prepare($branchCategoryItemQuery);

        // запрос листинга идентификаторов товаров
        $productIdPagerQuery = new Query\Product\GetIdPager($filterRepository->dumpRequestObjectList($requestFilters), $sorting, $region->id, ($pageNum - 1) * $limit, $limit);
        $curl->prepare($productIdPagerQuery);

        // запрос дерева категорий для меню
        $categoryListQuery = new Query\Product\Category\GetTreeList($region->id, 3);
        $curl->prepare($categoryListQuery);

        $curl->execute();

        // фильтры
        $filters = $filterRepository->getObjectListByQuery($filterListQuery);

        // предки и дети категории
        $productCategoryRepository->setBranchForObjectByQuery($category, $branchCategoryItemQuery);

        // листинг идентификаторов товаров
        $productIdPager = (new Repository\Product\IdPager())->getObjectByQuery($productIdPagerQuery);

        // запрос списка товаров
        $productListQuery = null;
        if ((bool)$productIdPager->ids) {
            $productListQuery = new Query\Product\GetListByIdList($productIdPager->ids, $region->id);
            $curl->prepare($productListQuery);
        }

        // запрос меню
        $mainMenuQuery = new Query\MainMenu\GetItem();
        $curl->prepare($mainMenuQuery);

        // запрос настроек каталога
        $catalogConfigQuery = new Query\Product\Catalog\Config\GetItemByProductCategoryObject(array_merge($category->ascendants, [$category]));
        $curl->prepare($catalogConfigQuery);

        // запрос списка рейтингов товаров
        $ratingListQuery = null;
        if ($config->productReview->enabled && (bool)$productIdPager->ids) {
            $ratingListQuery = new Query\Product\Rating\GetListByProductIdList($productIdPager->ids);
            $curl->prepare($ratingListQuery);
        }

        // запрос списка видео для товаров
        $videoGroupedListQuery = new Query\Product\Media\Video\GetGroupedListByProductIdList($productIdPager->ids);
        $curl->prepare($videoGroupedListQuery);

        $curl->execute();

        // список товаров
        $productsById = $productListQuery ? $productRepository->getIndexedObjectListByQueryList([$productListQuery]) : [];

        // меню
        $mainMenu = (new Repository\MainMenu())->getObjectByQuery($mainMenuQuery, $categoryListQuery);

        // настройки каталога
        $catalogConfig = (new Repository\Product\Catalog\Config())->getObjectByQuery($catalogConfigQuery);

        // список рейтингов товаров
        if ($ratingListQuery) {
            $productRepository->setRatingForObjectListByQuery($productsById, $ratingListQuery);
        }

        // список видео для товаров
        $productRepository->setVideoForObjectListByQuery($productsById, $videoGroupedListQuery);

        // запрос для получения страницы
        $pageRequest = new Repository\Page\ProductCatalog\ChildCategory\Request();
        $pageRequest->region = $region;
        $pageRequest->mainMenu = $mainMenu;
        $pageRequest->pageNum = $pageNum;
        $pageRequest->limit = $limit;
        $pageRequest->count = $productIdPager->count; // TODO: передавать productIdPager
        $pageRequest->requestFilters = $requestFilters;
        $pageRequest->filters = $filters;
        $pageRequest->sorting = $sorting;
        $pageRequest->sortings = $sortings;
        $pageRequest->category = $category;
        $pageRequest->catalogConfig = $catalogConfig;
        $pageRequest->products = $productsById;
        $pageRequest->httpRequest = $request;

        // страница
        $page = new Page();
        (new Repository\Page\ProductCatalog\ChildCategory())->buildObjectByRequest($page, $pageRequest);

        // debug
        if ($config->debugLevel) $this->getDebugContainer()->page = $page;
        //die(json_encode($page, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        // рендер
        $renderer = $this->getRenderer();
        $renderer->setPartials([
            'content' => 'page/product-catalog/child-category/content',
            //'content' => file_get_contents($this->getConfig()->mustacheRenderer->templateDir . '/page/product-catalog/child-category/content.mustache'),
        ]);
        $content = $renderer->render('layout/default', $page);

        // http-ответ
        $response = new Http\Response($content);

        return $response;
    }
}