<?php

namespace Enter\Site\Action\Page\ProductCatalog;

use Enter\Http\Response;
use Enter\Site\ConfigTrait;
use Enter\Site\CurlClientTrait;
use Enter\Http\Request;
use Enter\Site\Action\PageNum\GetByHttpRequest as GetPageNum;
use Enter\Site\Action\Region\GetObjectByQuery as GetRegion;
use Enter\Site\Action\Product\Category\GetTokenByHttpRequest as GetProductCategoryToken;
use Enter\Site\Action\Product\Category\GetObjectByQuery as GetCategory;
use Enter\Site\Action\Product\Filter\GetRequestObjectListByHttpRequest as GetRequestFilterList;
use Enter\Site\Action\Product\Sorting\GetObjectByHttpRequest as GetSorting;
use Enter\Site\Action\Product\IdPager\GetObjectByQuery as GetProductIdPagerByQuery;
use Enter\Site\Action\Product\Category\GetAncestryObjectByQuery as GetAncestryCategory;
use Enter\Site\Action\Product\GetObjectListByQuery as GetProductList;
use Enter\Site\Action\Product\Catalog\Config\GetObjectByQuery as GetCatalogConfig;
use Enter\Site\Curl\Query\Product\Category\GetItemByToken as GetCoreCategoryQuery;
use Enter\Site\Curl\Query\Product\Category\GetAdminItemByToken as GetAdminCategoryQuery;
use Enter\Site\Curl\Query\Region\GetItemByHttpRequest as GetRegionQuery;
use Enter\Site\Curl\Query\Product\Category\GetAncestryItemByCategoryObject as GetAncestryCategoryQuery;
use Enter\Site\Curl\Query\Product\GetIdPagerByRequestFilter as GetProductIdPagerQuery;
use Enter\Site\Curl\Query\Product\GetListByIdList as GetProductListQuery;
use Enter\Site\Curl\Query\Product\Catalog\Config\GetItemByProductCategoryObject as GetCatalogConfigQuery;

class GetChildCategoryByHttpRequest {
    use ConfigTrait;
    //use CurlClientTrait; // https://bugs.php.net/bug.php?id=63911
    use CurlClientTrait {
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