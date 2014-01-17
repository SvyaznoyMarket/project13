<?php

namespace EnterSite\Action\ProductCatalog\ChildCategory;

use EnterSite\ConfigTrait;
use EnterSite\CurlClientTrait;
use Enter\Exception\PermanentlyRedirect;
use EnterSite\Action\Region\GetObjectByQuery as GetRegion;
use EnterSite\Action\Product\Category\GetObjectByQuery as GetCategory;
use EnterSite\Action\Product\IdPager\GetObjectByQuery as GetProductIdPagerByQuery;
use EnterSite\Action\Product\Category\GetAncestryObjectByQuery as GetAncestryCategory;
use EnterSite\Action\Product\GetObjectListByQuery as GetProductList;
use EnterSite\Action\Product\Catalog\Config\GetObjectByQuery as GetCatalogConfig;
use EnterSite\Curl\Query\Product\Category\GetItemByToken as GetCoreCategoryQuery;
use EnterSite\Curl\Query\Product\Category\GetAdminItemByToken as GetAdminCategoryQuery;
use EnterSite\Curl\Query\Region\GetItemById as GetRegionQuery;
use EnterSite\Curl\Query\Product\Category\GetAncestryItemByCategoryObject as GetAncestryCategoryQuery;
use EnterSite\Curl\Query\Product\GetIdPagerByRequestFilter as GetProductIdPagerQuery;
use EnterSite\Curl\Query\Product\GetListByIdList as GetProductListQuery;
use EnterSite\Curl\Query\Product\Catalog\Config\GetItemByProductCategoryObject as GetCatalogConfigQuery;
use EnterSite\Model\Page\ProductCatalog\ChildCategory as Page;
use EnterSite\Model\Page\ProductCatalog\ChildCategory;
use EnterSite\MustacheRendererTrait;

class GetPageByCategoryToken {
    use \EnterSite\ConfigTrait;
    //use CurlClientTrait; // https://bugs.php.net/bug.php?id=63911
    use \EnterSite\CurlClientTrait, MustacheRendererTrait {
        ConfigTrait::getConfig insteadof CurlClientTrait;
        ConfigTrait::getConfig insteadof MustacheRendererTrait;
    }

    public function execute(
        $regionId,
        $categoryToken,
        $pageNum,
        $requestFilters,
        $sorting
    ) {
        $config = $this->getConfig();
        $curl = $this->getCurlClient();

        // запрос региона
        $regionQuery = new GetRegionQuery($regionId);
        $curl->prepare($regionQuery);

        $curl->execute();

        // регион
        $region = (new GetRegion())->execute($regionQuery);

        // запрос категории
        $productCategoryItemQuery = new GetCoreCategoryQuery($categoryToken, $region);
        $curl->prepare($productCategoryItemQuery);

        $productCategoryAdminItemQuery = null;
        if ($config->adminService->enabled) {
            $productCategoryAdminItemQuery = new GetAdminCategoryQuery($categoryToken, $region);
            $curl->prepare($productCategoryAdminItemQuery);
        }

        $curl->execute();

        // категория
        $category = (new GetCategory())->execute($productCategoryItemQuery, $productCategoryAdminItemQuery);
        if ($category->redirectLink) {
            $redirect = new PermanentlyRedirect();
            $redirect->setLink($category->redirectLink);

            throw $redirect;
        }

        // запрос предка категории
        $ancestryCategoryItemQuery = new GetAncestryCategoryQuery($category, $region);
        $curl->prepare($ancestryCategoryItemQuery);

        $curl->execute();

        // предок категории
        $ancestryCategory = (new GetAncestryCategory())->execute($ancestryCategoryItemQuery);

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

        // запрос списка товаров
        $productListQuery = new GetProductListQuery($productIdPager->id, $region);
        $curl->prepare($productListQuery);

        $curl->execute();

        // список товаров
        $products = (new GetProductList())->execute($productListQuery);

        $page = new Page(
            $region,
            $category,
            $products
        );

        return $page;
    }
}