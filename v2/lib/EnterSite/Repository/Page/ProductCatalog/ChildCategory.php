<?php

namespace EnterSite\Repository\Page\ProductCatalog;

use Enter\Exception\PermanentlyRedirect;
use EnterSite\ConfigTrait;
use EnterSite\CurlClientTrait;
use EnterSite\MustacheRendererTrait;
use EnterSite\Repository;
use EnterSite\Curl\Query;
use EnterSite\Model;

class ChildCategory {
    use ConfigTrait;
    //use CurlClientTrait; // https://bugs.php.net/bug.php?id=63911
    use CurlClientTrait, MustacheRendererTrait {
        ConfigTrait::getConfig insteadof CurlClientTrait;
        ConfigTrait::getConfig insteadof MustacheRendererTrait;
    }

    /**
     * @param string $categoryToken
     * @param string $regionId
     * @param int $pageNum
     * @param Model\Product\RequestFilter[] $requestFilters
     * @param Model\Product\Sorting $sorting
     * @return Model\Page\ProductCatalog\ChildCategory
     * @throws PermanentlyRedirect
     */
    public function getObjectByToken(
        $categoryToken,
        $regionId,
        $pageNum,
        $requestFilters,
        $sorting
    ) {
        $config = $this->getConfig();
        $curl = $this->getCurlClient();

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
            $redirect = new PermanentlyRedirect();
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

        // страница
        $page = new Model\Page\ProductCatalog\ChildCategory();
        //die(json_encode($page, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        return $page;
    }
}