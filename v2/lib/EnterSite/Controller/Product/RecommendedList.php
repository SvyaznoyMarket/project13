<?php

namespace EnterSite\Controller\Product;

use Enter\Http;
use EnterSite\ConfigTrait;
use EnterSite\CurlClientTrait;
use EnterSite\LoggerTrait;
use EnterSite\MustacheRendererTrait;
use EnterSite\Repository;
use EnterSite\Curl\Query;
use EnterSite\Model;
use EnterSite\Model\Page\Product\RecommendedList as Page;

class RecommendedList {
    use ConfigTrait, LoggerTrait, CurlClientTrait, MustacheRendererTrait {
        ConfigTrait::getConfig insteadof LoggerTrait, CurlClientTrait, MustacheRendererTrait;
        LoggerTrait::getLogger insteadof CurlClientTrait;
    }

    public function execute(Http\Request $request) {
        $logger = $this->getLogger();
        $config = $this->getConfig();
        $curl = $this->getCurlClient();
        $productRepository = new Repository\Product();

        // ид региона
        $regionId = (new Repository\Region())->getIdByHttpRequestCookie($request);

        // ид товара
        $productId = $productRepository->getIdByHttpRequest($request);

        // запрос региона
        $regionQuery = new Query\Region\GetItemById($regionId);
        $curl->prepare($regionQuery);

        $curl->execute();

        // регион
        $region = (new Repository\Region())->getObjectByQuery($regionQuery);

        // запрос товара
        $productItemQuery = new Query\Product\GetItemById($productId, $region->id);
        $curl->prepare($productItemQuery);

        $curl->execute();

        // товар
        $product = $productRepository->getObjectByQuery($productItemQuery);

        // запрос идетификаторов товаров "с этим товаром также покупают"
        $crossSellItemToItemsListQuery = new Query\Product\Relation\CrossSellItemToItems\GetIdListByProductId($product->id);
        $curl->prepare($crossSellItemToItemsListQuery);

        // запрос идетификаторов товаров "похожие товары"
        //$upSellItemToItemsListQuery = new Query\Product\Relation\UpSellItemToItems\GetIdListByProductId($product->id);
        /** @var \Enter\Curl\Query|null $upSellItemToItemsListQuery */
        $upSellItemToItemsListQuery = null;
        if ($upSellItemToItemsListQuery) {
            $curl->prepare($upSellItemToItemsListQuery);
        }

        // запрос идетификаторов товаров "с этим товаром также смотрят"
        //$itemToItemsListQuery = new Query\Product\Relation\ItemToItems\GetIdListByProductId($product->id);
        /** @var \Enter\Curl\Query|null $itemToItemsListQuery */
        $itemToItemsListQuery = null;
        if ($itemToItemsListQuery) {
            $curl->prepare($itemToItemsListQuery);
        }

        $curl->execute();

        // идетификаторы товаров "с этим товаром также покупают"
        $alsoBoughtIdList = $product->relatedIds;
        try {
            $alsoBoughtIdList = array_unique(array_merge($alsoBoughtIdList, $crossSellItemToItemsListQuery->getResult()));
        } catch (\Exception $e) {
            $logger->push(['type' => 'warn', 'error' => $e, 'action' => __METHOD__, 'tag' => ['product.recommendation']]);
        }

        // идетификаторы товаров "похожие товары"
        $similarIdList = [];
        try {
            $similarIdList = $upSellItemToItemsListQuery ? array_unique($upSellItemToItemsListQuery->getResult()) : [];
        } catch (\Exception $e) {
            $logger->push(['type' => 'warn', 'error' => $e, 'action' => __METHOD__, 'tag' => ['product.recommendation']]);
        }

        // идетификаторы товаров "с этим товаром также смотрят"
        $alsoViewedIdList = [];
        try {
            $alsoViewedIdList = $itemToItemsListQuery ? array_unique($itemToItemsListQuery->getResult()) : [];
        } catch (\Exception $e) {
            $logger->push(['type' => 'warn', 'error' => $e, 'action' => __METHOD__, 'tag' => ['product.recommendation']]);
        }

        // список всех идентификаторов товаров
        $productIds = array_unique(array_merge($alsoBoughtIdList, $similarIdList, $alsoViewedIdList));

        // запрос списка товаров
        $productListQueries = [];
        foreach (array_chunk($productIds, $config->curl->queryChunkSize) as $idsInChunk) {
            $productListQuery = new Query\Product\GetListByIdList($idsInChunk, $region->id);
            $curl->prepare($productListQuery);

            $productListQueries[] = $productListQuery;
        }

        $curl->execute();

        // товары
        $productsById = $productRepository->getIndexedObjectListByQueryList($productListQueries);

        foreach ($alsoBoughtIdList as $i => $productId) {
            // SITE-2818 из списка товаров "с этим товаром также покупают" убираем товары, которые есть только в магазинах
            /** @var Model\Product|null $product */
            $product = isset($productsById[$productId]) ? $productsById[$productId] : null;
            if (!$product) continue;

            if ($product->isInShopOnly || !$product->isBuyable) {
                unset($alsoBoughtIdList[$i]);
            }
        }

        $chunkedIds = [$alsoBoughtIdList, $similarIdList, $alsoViewedIdList];
        $ids = [];
        foreach ($chunkedIds as &$ids) {
            // удаляем ид товаров, которых нет в массиве $productsById
            $ids = array_intersect($ids, array_keys($productsById));
            // применяем лимит
            $ids = array_slice($ids, 0, $config->product->itemsInSlider);
        }
        unset($ids, $chunkedIds);

        // запрос для получения страницы
        $pageRequest = new Repository\Page\Product\RecommendedList\Request();
        $pageRequest->product = $product;
        $pageRequest->productsById = $productsById;
        $pageRequest->alsoBoughtIdList = $alsoBoughtIdList;
        $pageRequest->alsoViewedIdList = $alsoViewedIdList;
        $pageRequest->similarIdList = $similarIdList;
        //die(json_encode($pageRequest, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        // страница
        $page = new Page();
        (new Repository\Page\Product\RecommendedList())->buildObjectByRequest($page, $pageRequest);

        //die(json_encode($page, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        return new Http\JsonResponse([
            'result' => $page,
        ]);
    }
}