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
use EnterTerminal\Model\Page\ProductCatalog\ChildCategory as Page;

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

        $curl->execute(1, 2);

        // магазин
        $shop = (new Repository\Shop())->getObjectByQuery($shopItemQuery);
        if (!$shop) {
            throw new \Exception(sprintf('Магазин #%s не найден', $shopId));
        }

        // запрос категории
        $categoryItemQuery = new Query\Product\Category\GetTreeItemById($categoryId, $shop->regionId);
        $curl->prepare($categoryItemQuery);

        $categoryAdminItemQuery = null;

        $curl->execute(1, 2);

        // категория
        $category = (new Repository\Product\Category())->getObjectByQuery($categoryItemQuery, $categoryAdminItemQuery);
        if (!$category) {
            return (new Controller\Error\NotFound())->execute($request, sprintf('Категория товара #%s не найдена', $categoryId));
        }

        // фильтры в запросе
        // TODO: доделать фильтры
        $requestFilters = []; //(new Repository\Product\Filter())->getRequestObjectListByHttpRequest($request);
        $requestFilters['category'] = new Model\Product\RequestFilter();
        $requestFilters['category']->value = $category->id; // TODO: Model\Product\RequestFilterCollection::offsetSet

        // запрос предка категории
        $ascendantCategoryItemQuery = new Query\Product\Category\GetAscendantItemByCategoryObject($category, $shop->regionId);
        $curl->prepare($ascendantCategoryItemQuery);

        // запрос листинга идентификаторов товаров
        $productIdPagerQuery = new Query\Product\GetIdPagerByRequestFilter($requestFilters, $sorting, $shop->regionId, ($pageNum - 1) * $limit, $limit);
        $curl->prepare($productIdPagerQuery);

        $curl->execute(1, 2);

        // предки категории
        $category->ascendants = (new Repository\Product\Category())->getAscendantListByQuery($ascendantCategoryItemQuery);

        // листинг идентификаторов товаров
        $productIdPager = (new Repository\Product\IdPager())->getObjectByQuery($productIdPagerQuery);

        // запрос списка товаров
        $productListQuery = new Query\Product\GetListByIdList($productIdPager->ids, $shop->regionId);
        $curl->prepare($productListQuery);

        // запрос настроек каталога
        $catalogConfigQuery = new Query\Product\Catalog\Config\GetItemByProductCategoryObject(array_merge($category->ascendants, [$category]));
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
        $productsById = $productRepository->getIndexedObjectListByQueryList([$productListQuery]);

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

        return new Http\JsonResponse($page);
    }
}