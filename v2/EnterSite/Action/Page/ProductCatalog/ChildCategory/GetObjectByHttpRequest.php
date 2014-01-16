<?php

namespace EnterSite\Action\Page\ProductCatalog\ChildCategory;

use Enter\Http\Response;
use EnterSite\ConfigTrait;
use EnterSite\CurlClientTrait;
use Enter\Http\Request;
use EnterSite\Action\PageNum\GetByHttpRequest as GetPageNum;
use EnterSite\Action\Region\GetObjectByQuery as GetRegion;
use EnterSite\Action\Product\Category\GetTokenByHttpRequest as GetProductCategoryToken;
use EnterSite\Action\Product\Category\GetObjectByQuery as GetCategory;
use EnterSite\Action\Product\Filter\GetRequestObjectListByHttpRequest as GetRequestFilterList;
use EnterSite\Action\Product\Sorting\GetObjectByHttpRequest as GetSorting;
use EnterSite\Action\Product\IdPager\GetObjectByQuery as GetProductIdPagerByQuery;
use EnterSite\Action\Product\Category\GetAncestryObjectByQuery as GetAncestryCategory;
use EnterSite\Action\Product\GetObjectListByQuery as GetProductList;
use EnterSite\Action\Product\Catalog\Config\GetObjectByQuery as GetCatalogConfig;
use EnterSite\Curl\Query\Product\Category\GetItemByToken as GetCoreCategoryQuery;
use EnterSite\Curl\Query\Product\Category\GetAdminItemByToken as GetAdminCategoryQuery;
use EnterSite\Curl\Query\Region\GetItemByHttpRequest as GetRegionQuery;
use EnterSite\Curl\Query\Product\Category\GetAncestryItemByCategoryObject as GetAncestryCategoryQuery;
use EnterSite\Curl\Query\Product\GetIdPagerByRequestFilter as GetProductIdPagerQuery;
use EnterSite\Curl\Query\Product\GetListByIdList as GetProductListQuery;
use EnterSite\Curl\Query\Product\Catalog\Config\GetItemByProductCategoryObject as GetCatalogConfigQuery;

class GetObjectByHttpRequest {
    use \EnterSite\ConfigTrait;
    //use CurlClientTrait; // https://bugs.php.net/bug.php?id=63911
    use \EnterSite\CurlClientTrait {
        ConfigTrait::getConfig insteadof CurlClientTrait;
    }

    public function execute(Request $request) {
        $config = $this->getConfig();
        $curl = $this->getCurlClient();

        // токен категории
        $productCategoryToken = (new GetProductCategoryToken())->execute($request);

        // номер страницы
        $pageNum = (new GetPageNum())->execute($request);

        // запрос региона
        $regionQuery = new GetRegionQuery($request);
        $curl->prepare($regionQuery);

        $curl->execute();

        // регион
        $region = (new GetRegion())->execute($regionQuery);

        // запрос категории
        $productCategoryItemQuery = new GetCoreCategoryQuery($productCategoryToken, $region);
        $curl->prepare($productCategoryItemQuery);

        $productCategoryAdminItemQuery = null;
        if ($config->adminService->enabled) {
            $productCategoryAdminItemQuery = new GetAdminCategoryQuery($productCategoryToken, $region);
            $curl->prepare($productCategoryAdminItemQuery);
        }

        $curl->execute();

        // категория
        $category = (new GetCategory())->execute($productCategoryItemQuery, $productCategoryAdminItemQuery);
        if ($category->redirectLink) {
            return new Response($category->redirectLink, Response::STATUS_MOVED_PERMANENTLY);
        }

        // запрос предка категории
        $ancestryCategoryItemQuery = new GetAncestryCategoryQuery($category, $region);
        $curl->prepare($ancestryCategoryItemQuery);

        $curl->execute();

        // предок категории
        $ancestryCategory = (new GetAncestryCategory())->execute($ancestryCategoryItemQuery);

        // фильтры в запросе
        $requestFilters = (new GetRequestFilterList())->execute($request);

        // сортировка
        $sorting = (new GetSorting())->execute($request);

        // запрос настроек каталога
        $catalogConfigQuery = new GetCatalogConfigQuery($ancestryCategory);
        $curl->prepare($catalogConfigQuery);

        // запрос листинга идентификаторов товаров
        $limit = $config->productList->itemPerPage;
        $productIdPagerQuery = new GetProductIdPagerQuery($requestFilters, $sorting, $region, ($pageNum - 1) * $limit, $limit);
        $curl->prepare($productIdPagerQuery);

        $curl->execute();

        // листинг идентификаторов товаров
        $productIdPager = (new GetProductIdPagerByQuery())->execute($productIdPagerQuery);

        // настройки каталога
        $catalogConfig = (new GetCatalogConfig())->execute($catalogConfigQuery);
        var_dump($catalogConfig);

        // запрос списка товаров
        $productListQuery = new GetProductListQuery($productIdPager->id, $region);
        $curl->prepare($productListQuery);

        $curl->execute();

        // список товаров
        $products = (new GetProductList())->execute($productListQuery);
    }
}