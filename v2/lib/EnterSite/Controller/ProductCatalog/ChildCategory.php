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

class ChildCategory {
    use ConfigTrait;
    use CurlClientTrait, MustacheRendererTrait {
        ConfigTrait::getConfig insteadof CurlClientTrait;
        ConfigTrait::getConfig insteadof MustacheRendererTrait;
    }

    /**
     * @param Http\Request $request
     * @return Http\Response
     * @throws Exception\PermanentlyRedirect
     */
    public function execute(Http\Request $request) {
        $config = $this->getConfig();
        $curl = $this->getCurlClient();

        // ид региона
        $regionId = (new Repository\Region())->getIdByHttpRequest($request);

        // токен категории
        $categoryToken = (new Repository\Product\Category())->getTokenByHttpRequest($request);

        // номер страницы
        $pageNum = (new Repository\PageNum())->getByHttpRequest($request);

        // фильтры в запросе
        $requestFilters = (new Repository\Product\Filter())->getRequestObjectListByHttpRequest($request);

        // сортировка
        $sorting = (new Repository\Product\Sorting())->getObjectByHttpRequest($request);

        // запрос региона
        $regionQuery = new Query\Region\GetItemById($regionId);
        $curl->prepare($regionQuery);

        $curl->execute();

        // регион
        $region = (new Repository\Region())->getObjectByQuery($regionQuery);

        // запрос категории
        $productCategoryItemQuery = new Query\Product\Category\GetItemByToken($categoryToken, $region);
        $curl->prepare($productCategoryItemQuery);

        $productCategoryAdminItemQuery = null;
        if ($config->adminService->enabled) {
            $productCategoryAdminItemQuery = new Query\Product\Category\GetAdminItemByToken($categoryToken, $region);
            $curl->prepare($productCategoryAdminItemQuery);
        }

        $curl->execute();

        // категория
        $category = (new Repository\Product\Category())->getObjectByQuery($productCategoryItemQuery, $productCategoryAdminItemQuery);
        if ($category->redirectLink) {
            $redirect = new Exception\PermanentlyRedirect();
            $redirect->setLink($category->redirectLink);

            throw $redirect;
        }

        // запрос предка категории
        $ancestryCategoryItemQuery = new Query\Product\Category\GetAncestryItemByCategoryObject($category, $region);
        $curl->prepare($ancestryCategoryItemQuery);

        $curl->execute();

        // предок категории
        $ancestryCategory = (new Repository\Product\Category())->getAncestryObjectByQuery($ancestryCategoryItemQuery);

        // запрос настроек каталога
        $catalogConfigQuery = new Query\Product\Catalog\Config\GetItemByProductCategoryObject($ancestryCategory);
        $curl->prepare($catalogConfigQuery);

        // запрос листинга идентификаторов товаров
        $limit = $config->productList->itemPerPage;
        $productIdPagerQuery = new Query\Product\GetIdPagerByRequestFilter($requestFilters, $sorting, $region, ($pageNum - 1) * $limit, $limit);
        $curl->prepare($productIdPagerQuery);

        $curl->execute();

        // листинг идентификаторов товаров
        $productIdPager = (new Repository\Product\IdPager())->getObjectByQuery($productIdPagerQuery);

        // настройки каталога
        $catalogConfig = (new Repository\Product\Catalog\Config())->getObjectByQuery($catalogConfigQuery);

        // запрос списка товаров
        $productListQuery = new Query\Product\GetListByIdList($productIdPager->id, $region);
        $curl->prepare($productListQuery);

        $curl->execute();

        // список товаров
        $products = (new Repository\Product())->getObjectListByQuery($productListQuery);

        // запрос для получения страницы
        $pageRequest = new Repository\Page\ProductCatalog\ChildCategory\Request();
        $pageRequest->pageNum = $pageNum;
        $pageRequest->requestFilters = $requestFilters;
        $pageRequest->sorting = $sorting;
        $pageRequest->region = $region;
        $pageRequest->category = $category;
        $pageRequest->catalogConfig = $catalogConfig;
        $pageRequest->products = $products;

        // страница
        $page = (new Repository\Page\ProductCatalog\ChildCategory())->getObjectByRequest($pageRequest);
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