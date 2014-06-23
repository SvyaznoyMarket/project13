<?php

namespace EnterTerminal\Controller;

use Enter\Http;
use EnterTerminal\ConfigTrait;
use EnterSite\CurlClientTrait;
use EnterSite\MustacheRendererTrait;
use EnterSite\Controller;
use EnterSite\Repository;
use EnterSite\Curl\Query;
use EnterSite\Model;
use EnterTerminal\Model\Page\Search as Page;

class Search {
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

        // ид категории
        // поисковая строка
        $searchPhrase = (new Repository\Search())->getPhraseByHttpRequest($request, 'phrase');
        if (!$searchPhrase) {
            throw new \Exception('Не передана поисковая фраза');
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

        // фильтры в http-запросе
        $requestFilters = $filterRepository->getRequestObjectListByHttpRequest($request);
        $filterData = $filterRepository->dumpRequestObjectList($requestFilters);
        // фильтр поисковой фразы
        $requestFilters[] = $filterRepository->getRequestObjectBySearchPhrase($searchPhrase);

        // запрос фильтров
        $filterListQuery = new Query\Product\Filter\GetListBySearchPhrase($searchPhrase, $shop->regionId);
        $curl->prepare($filterListQuery);

        // запрос результатов поиска
        $searchResultQuery = new Query\Search\GetItemByPhrase($searchPhrase, $filterData, $sorting, $shop->regionId, ($pageNum - 1) * $limit, $limit);
        $curl->prepare($searchResultQuery);

        $curl->execute();

        // фильтры
        $filters = $filterRepository->getObjectListByQuery($filterListQuery);
        // значения для фильтров
        $filterRepository->setValueForObjectList($filters, $requestFilters);

        // листинг идентификаторов товаров
        $searchResult = (new Repository\Search())->getObjectByQuery($searchResultQuery);

        // TODO: убрать когда поиск будет возвращать картинки категорий
        $categoryListQuery =
            (bool)$searchResult->categories
            ? new Query\Product\Category\GetListByIdList(
                array_map(function(Model\SearchResult\Category $category) { return $category->id; }, $searchResult->categories),
                $shop->regionId
            )
            : null;
        if ($categoryListQuery) {
            $curl->prepare($categoryListQuery)->execute();
        }

        // фильтры
        $filters = $filterRepository->getObjectListByQuery($filterListQuery);
        $filters[] = new Model\Product\Filter([
            'filter_id' => 'phrase',
            'name'      => 'Поисковая строка',
            'type_id'   => Model\Product\Filter::TYPE_STRING,
            'options'   => [
                ['id' => null],
            ],
        ]);
        // добавление фильтров категории
        //$categories = (new Repository\Product\Category())->getObjectListBySearchResult($searchResult); // TODO: вернуть когда поиск будет возвращать картинки категорий
        $categories = $categoryListQuery ? (new Repository\Product\Category())->getObjectListByQuery($categoryListQuery) : [];
        $categoryFilters = $filterRepository->getObjectListByCategoryList($categories);
        $filters = array_merge($filters, $categoryFilters);

        // значения для фильтров
        $filterRepository->setValueForObjectList($filters, $requestFilters);

        // запрос списка товаров
        $productListQuery = null;
        if ((bool)$searchResult->productIds) {
            $productListQuery = new Query\Product\GetListByIdList($searchResult->productIds, $shop->regionId);
            $curl->prepare($productListQuery);
        }

        // запрос списка рейтингов товаров
        $ratingListQuery = null;
        if ($config->productReview->enabled && (bool)$searchResult->productIds) {
            $ratingListQuery = new Query\Product\Rating\GetListByProductIdList($searchResult->productIds);
            $curl->prepare($ratingListQuery);
        }

        // запрос списка видео для товаров
        $videoGroupedListQuery = new Query\Product\Media\Video\GetGroupedListByProductIdList($searchResult->productIds);
        $curl->prepare($videoGroupedListQuery);

        $curl->execute();

        // список товаров
        $productsById = $productListQuery ? $productRepository->getIndexedObjectListByQueryList([$productListQuery]) : [];

        // список рейтингов товаров
        if ($ratingListQuery) {
            $productRepository->setRatingForObjectListByQuery($productsById, $ratingListQuery);
        }

        // список видео для товаров
        $productRepository->setVideoForObjectListByQuery($productsById, $videoGroupedListQuery);

        // страница
        $page = new Page();
        $page->searchPhrase = $searchPhrase;
        $page->products = array_values($productsById);
        $page->productCount = $searchResult->productCount;
        $page->filters = $filters;
        $page->sortings = $sortings;

        return new Http\JsonResponse($page);
    }
}