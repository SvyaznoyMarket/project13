<?php

namespace EnterTerminal\Controller\ProductCatalog;

use Enter\Http;
use EnterTerminal\ConfigTrait;
use EnterSite\CurlClientTrait;
use EnterSite\MustacheRendererTrait;
use EnterSite\Controller;
use EnterSite\Repository;
use EnterSite\Curl\Query;
use EnterSite\Model;
use EnterTerminal\Model\Page\ProductCatalog\Category as Page;

class Category {
    use ConfigTrait, CurlClientTrait {
        ConfigTrait::getConfig insteadof CurlClientTrait;
    }

    /**
     * @param Http\Request $request
     * @throws \Exception
     * @return Http\Response
     */
    public function execute(Http\Request $request) {
        $config = $this->getConfig();
        $curl = $this->getCurlClient();
        $productRepository = new Repository\Product();
        $productCategoryRepository = new Repository\Product\Category();
        $filterRepository = new \EnterTerminal\Repository\Product\Filter(); // FIXME

        // ид магазина
        $shopId = (new \EnterTerminal\Repository\Shop())->getIdByHttpRequest($request); // FIXME

        // ид товара
        $categoryId = trim((string)$request->query['categoryId']);
        if (!$categoryId) {
            throw new \Exception('Не указан параметр categoryId');
        }

        // номер страницы
        $pageNum = (int)$request->query['page'] ?: 1;

        // количество товаров на страницу
        $limit = (int)$request->query['limit'] ?: 10;

        // сортировки
        $sortings = (new Repository\Product\Sorting())->getObjectList();

        // сортировка
        $sorting = null;
        if (!empty($request->query['sort']['token']) && !empty($request->query['sort']['direction'])) {
            $sorting = new Model\Product\Sorting();
            $sorting->token = trim((string)$request->query['sort']['token']);
            $sorting->direction = trim((string)$request->query['sort']['direction']);
        }

        // запрос региона
        $shopItemQuery = new Query\Shop\GetItemById($shopId);
        $curl->prepare($shopItemQuery);

        $curl->execute();

        // магазин
        $shop = (new Repository\Shop())->getObjectByQuery($shopItemQuery);
        if (!$shop) {
            throw new \Exception(sprintf('Магазин #%s не найден', $shopId));
        }

        // запрос категории
        $categoryItemQuery = new Query\Product\Category\GetTreeItemById($categoryId, $shop->regionId);
        $curl->prepare($categoryItemQuery);

        $curl->execute();

        // категория
        $category = $productCategoryRepository->getObjectByQuery($categoryItemQuery, null);
        if (!$category) {
            return (new Controller\Error\NotFound())->execute($request, sprintf('Категория товара #%s не найдена', $categoryId));
        }

        // запрос фильтров
        $filterListQuery = new Query\Product\Filter\GetListByCategoryId($category->id, $shop->regionId);
        $curl->prepare($filterListQuery);

        // фильтры в запросе
        $requestFilters = $filterRepository->getRequestObjectListByHttpRequest($request);
        // фильтр категории в http-запросе
        $requestFilters[] = $filterRepository->getRequestObjectByCategory($category);

        // запрос предка категории
        $ascendantCategoryItemQuery = new Query\Product\Category\GetAscendantItemByCategoryObject($category, $shop->regionId);
        $curl->prepare($ascendantCategoryItemQuery);

        // запрос родителя категории
        $parentCategoryItemQuery = null;
        if ($category->parentId) {
            $parentCategoryItemQuery = new Query\Product\Category\GetTreeItemById($category->parentId, $shop->regionId);
            $curl->prepare($parentCategoryItemQuery);
        }

        // запрос листинга идентификаторов товаров
        $productIdPagerQuery = new Query\Product\GetIdPagerByRequestFilter($filterRepository->dumpRequestObjectList($requestFilters), $sorting, $shop->regionId, ($pageNum - 1) * $limit, $limit);
        $curl->prepare($productIdPagerQuery);

        $curl->execute();

        // фильтры
        $filters = $filterRepository->getObjectListByQuery($filterListQuery);
        // значения для фильтров
        $filterRepository->setValueForObjectList($filters, $requestFilters);

        // предки категории
        $category->ascendants = $productCategoryRepository->getAscendantListByQuery($ascendantCategoryItemQuery);

        // родитель категории
        $category->parent = $parentCategoryItemQuery ? $productCategoryRepository->getObjectByQuery($parentCategoryItemQuery) : null;

        // листинг идентификаторов товаров
        $productIdPager = (new Repository\Product\IdPager())->getObjectByQuery($productIdPagerQuery);

        // запрос списка товаров
        $productListQuery = null;
        if ((bool)$productIdPager->ids) {
            $productListQuery = new Query\Product\GetListByIdList($productIdPager->ids, $shop->regionId);
            $curl->prepare($productListQuery);
        }

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

        // настройки каталога
        $catalogConfig = $catalogConfigQuery ? (new Repository\Product\Catalog\Config())->getObjectByQuery($catalogConfigQuery) : null;

        // список рейтингов товаров
        if ($ratingListQuery) {
            $productRepository->setRatingForObjectListByQuery($productsById, $ratingListQuery);
        }

        // список видео для товаров
        $productRepository->setVideoForObjectListByQuery($productsById, $videoGroupedListQuery);

        // страница
        $page = new Page();
        $page->category = $category;
        $page->catalogConfig = $catalogConfig;
        $page->products = array_values($productsById);
        $page->productCount = $productIdPager->count;
        $page->filters = $filters;
        $page->sortings = $sortings;

        return new Http\JsonResponse($page);
    }
}