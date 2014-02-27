<?php

namespace EnterSite\Controller\ProductCatalog;

use Enter\Exception;
use Enter\Http;
use EnterSite\ConfigTrait;
use EnterSite\CurlClientTrait;
use EnterSite\MustacheRendererTrait;
use EnterSite\Repository;
use EnterSite\Curl\Query;
use EnterSite\Model;
use EnterSite\Model\Page\ProductCatalog\ChildCategory as Page;

class ChildCategory {
    use ConfigTrait;
    use CurlClientTrait, MustacheRendererTrait {
        ConfigTrait::getConfig insteadof CurlClientTrait, MustacheRendererTrait;
    }

    /**
     * @param Http\Request $request
     * @return Http\Response
     * @throws Exception\PermanentlyRedirect
     */
    public function execute(Http\Request $request) {
        $config = $this->getConfig();
        $curl = $this->getCurlClient();
        $productRepository = new Repository\Product();

        // ид региона
        $regionId = (new Repository\Region())->getIdByHttpRequest($request);

        // токен категории
        $categoryToken = (new Repository\Product\Category())->getTokenByHttpRequest($request);

        // номер страницы
        $pageNum = (new Repository\PageNum())->getByHttpRequest($request);

        // сортировка
        $sorting = (new Repository\Product\Sorting())->getObjectByHttpRequest($request);

        // запрос региона
        $regionQuery = new Query\Region\GetItemById($regionId);
        $curl->prepare($regionQuery);

        $curl->execute(1, 2);

        // регион
        $region = (new Repository\Region())->getObjectByQuery($regionQuery);

        // запрос категории
        $categoryItemQuery = new Query\Product\Category\GetItemByToken($categoryToken, $region);
        $curl->prepare($categoryItemQuery);

        $categoryAdminItemQuery = null;
        if ($config->adminService->enabled) {
            $categoryAdminItemQuery = new Query\Product\Category\GetAdminItemByToken($categoryToken, $region);
            $curl->prepare($categoryAdminItemQuery);
        }

        $curl->execute(1, 2);

        // категория
        $category = (new Repository\Product\Category())->getObjectByQuery($categoryItemQuery, $categoryAdminItemQuery);
        if ($category->redirectLink) {
            $redirect = new Exception\PermanentlyRedirect();
            $redirect->setLink($category->redirectLink);

            throw $redirect;
        }

        // фильтры в запросе
        $requestFilters = (new Repository\Product\Filter())->getRequestObjectListByHttpRequest($request);
        $requestFilters['category'] = new Model\Product\RequestFilter();
        $requestFilters['category']->value = $category->id; // TODO: Model\Product\RequestFilterCollection::offsetSet

        // запрос предка категории
        $ancestryCategoryItemQuery = new Query\Product\Category\GetAncestryItemByCategoryObject($category, $region);
        $curl->prepare($ancestryCategoryItemQuery);

        // запрос листинга идентификаторов товаров
        $limit = $config->product->itemPerPage;
        $productIdPagerQuery = new Query\Product\GetIdPagerByRequestFilter($requestFilters, $sorting, $region, ($pageNum - 1) * $limit, $limit);
        $curl->prepare($productIdPagerQuery);

        // запрос дерева категорий для меню
        $categoryListQuery = new Query\Product\Category\GetTreeList($region, 3);
        $curl->prepare($categoryListQuery);

        $curl->execute(1, 2);

        // предок категории
        $ancestryCategory = (new Repository\Product\Category())->getAncestryObjectByQuery($ancestryCategoryItemQuery);

        // листинг идентификаторов товаров
        $productIdPager = (new Repository\Product\IdPager())->getObjectByQuery($productIdPagerQuery);

        // запрос списка товаров
        $productListQuery = new Query\Product\GetListByIdList($productIdPager->ids, $region);
        $curl->prepare($productListQuery);

        // запрос меню
        $mainMenuListQuery = new Query\MainMenu\GetList();
        $curl->prepare($mainMenuListQuery);

        // запрос настроек каталога
        $catalogConfigQuery = new Query\Product\Catalog\Config\GetItemByProductCategoryObject($ancestryCategory);
        $curl->prepare($catalogConfigQuery);

        // запрос списка рейтингов товаров
        $ratingListQuery = null;
        if ($config->productReview->enabled) {
            $ratingListQuery = new Query\Product\Rating\GetListByProductIdList($productIdPager->ids);
            $curl->prepare($ratingListQuery);
        }

        // запрос списка видео для товаров
        $videoGroupedListQuery = new Query\Product\Media\Video\GetGroupedListByProductIdList($productIdPager->ids);
        $curl->prepare($videoGroupedListQuery);

        $curl->execute(1, 2);

        // список товаров
        $productsById = $productRepository->getIndexedObjectListByQuery($productListQuery);

        // меню
        $mainMenuList = (new Repository\MainMenu())->getObjectListByQuery($mainMenuListQuery, $categoryListQuery);

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
        $pageRequest->mainMenuList = $mainMenuList;
        $pageRequest->region = $region;
        $pageRequest->pageNum = $pageNum;
        $pageRequest->requestFilters = $requestFilters;
        $pageRequest->sorting = $sorting;
        $pageRequest->category = $category;
        $pageRequest->catalogConfig = $catalogConfig;
        $pageRequest->products = $productsById;

        // страница
        $page = new Page();
        (new Repository\Page\ProductCatalog\ChildCategory())->buildObjectByRequest($page, $pageRequest);
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