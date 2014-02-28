<?php

namespace EnterSite\Controller\Product;

use Enter\Exception;
use Enter\Http;
use EnterSite\ConfigTrait;
use EnterSite\CurlClientTrait;
use EnterSite\LoggerTrait;
use EnterSite\MustacheRendererTrait;
use EnterSite\Repository;
use EnterSite\Curl\Query;
use EnterSite\Model;
//use EnterSite\Model\Page\Product\RecommendedList as Page;

class RecommendedList {
    use ConfigTrait;
    use LoggerTrait, CurlClientTrait, MustacheRendererTrait {
        ConfigTrait::getConfig insteadof LoggerTrait, CurlClientTrait, MustacheRendererTrait;
        LoggerTrait::getLogger insteadof CurlClientTrait;
    }

    public function execute(Http\Request $request) {
        $logger = $this->getLogger();
        $config = $this->getConfig();
        $curl = $this->getCurlClient();
        $productRepository = new Repository\Product();

        // ид региона
        $regionId = (new Repository\Region())->getIdByHttpRequest($request);

        // ид товара
        $productId = $productRepository->getIdByHttpRequest($request);

        // запрос региона
        $regionQuery = new Query\Region\GetItemById($regionId);
        $curl->prepare($regionQuery);

        $curl->execute(1, 2);

        // регион
        $region = (new Repository\Region())->getObjectByQuery($regionQuery);

        // запрос товара
        $productItemQuery = new Query\Product\GetItemById($productId, $region);
        $curl->prepare($productItemQuery);

        $curl->execute(1, 2);

        // товар
        $product = $productRepository->getObjectByQuery($productItemQuery);

        // запрос идетификаторов товаров "с этим товаром также покупают"
        $crossSellItemToItemsListQuery = new Query\Product\Relation\CrossSellItemToItems\GetIdListByProductId($product->id);
        $curl->prepare($crossSellItemToItemsListQuery);

        // запрос идетификаторов товаров "похожие товары"
        $upSellItemToItemsListQuery = new Query\Product\Relation\UpSellItemToItems\GetIdListByProductId($product->id);
        $curl->prepare($upSellItemToItemsListQuery);

        // запрос идетификаторов товаров "с этим товаром также смотрят"
        $itemToItemsListQuery = new Query\Product\Relation\ItemToItems\GetIdListByProductId($product->id);
        $curl->prepare($itemToItemsListQuery);

        $curl->execute(1, 2);

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
            $similarIdList = array_unique($upSellItemToItemsListQuery->getResult());
        } catch (\Exception $e) {
            $logger->push(['type' => 'warn', 'error' => $e, 'action' => __METHOD__, 'tag' => ['product.recommendation']]);
        }

        // идетификаторы товаров "с этим товаром также смотрят"
        $alsoViewedIdList = [];
        try {
            $alsoViewedIdList = array_unique($itemToItemsListQuery->getResult());
        } catch (\Exception $e) {
            $logger->push(['type' => 'warn', 'error' => $e, 'action' => __METHOD__, 'tag' => ['product.recommendation']]);
        }

        // список всех идентификаторов товаров
        $productIds = array_unique(array_merge($alsoBoughtIdList, $similarIdList, $alsoViewedIdList));

        // запрос списка товаров
        $productListQueries = [];
        foreach (array_chunk($productIds, $config->curl->queryChunkSize) as $idsInChunk) {
            $productListQuery = new Query\Product\GetListByIdList($idsInChunk, $region);
            $curl->prepare($productListQuery);

            $productListQueries[] = $productListQuery;
        }

        $curl->execute(1, 2);
        die(var_dump($alsoBoughtIdList));

        // товары
        $productsById = $productRepository->getIndexedObjectListByQueryList($productListQueries);

        foreach ($alsoBoughtIdList as $productId) {
            // SITE-2818 из списка товаров "с этим товаром также покупают" убираем товары, которые есть только в магазинах
            /** @var Model\Product|null $product */
            $product = isset($productsById[$productId]) ? $productsById[$productId] : null;
            if (!$product) continue;

            if ($product->isInShopOnly || !$product->isBuyable) {
                unset($alsoBoughtIdList[$productId]);
            }
        }

        die(var_dump(array_keys($productsById)));
    }
}