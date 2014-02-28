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

        // запрос "с этим товаром также покупают"
        $crossSellItemToItemsListQuery = new Query\Product\Relation\CrossSellItemToItems\GetIdListByProductId($product->id);
        $curl->prepare($crossSellItemToItemsListQuery);

        // запрос "похожие товары"
        $upSellItemToItemsListQuery = new Query\Product\Relation\UpSellItemToItems\GetIdListByProductId($product->id);
        $curl->prepare($upSellItemToItemsListQuery);

        // запрос "с этим товаром также смотрят"
        $itemToItemsListQuery = new Query\Product\Relation\ItemToItems\GetIdListByProductId($product->id);
        $curl->prepare($itemToItemsListQuery);

        $curl->execute(1, 2);

        // "с этим товаром также покупают"
        $alsoBoughtIdList = $product->relatedIds;
        try {
            $alsoBoughtIdList = array_unique(array_merge($alsoBoughtIdList, $crossSellItemToItemsListQuery->getResult()));
        } catch (\Exception $e) {
            $logger->push(['type' => 'warn', 'error' => $e, 'action' => __METHOD__, 'tag' => ['product.recommendation']]);
        }

        // "похожие товары"
        $similarIdList = [];
        try {
            $similarIdList = array_unique($upSellItemToItemsListQuery->getResult());
        } catch (\Exception $e) {
            $logger->push(['type' => 'warn', 'error' => $e, 'action' => __METHOD__, 'tag' => ['product.recommendation']]);
        }

        // "с этим товаром также смотрят"
        $alsoViewedIdList = [];
        try {
            $alsoViewedIdList = array_unique($itemToItemsListQuery->getResult());
        } catch (\Exception $e) {
            $logger->push(['type' => 'warn', 'error' => $e, 'action' => __METHOD__, 'tag' => ['product.recommendation']]);
        }

        // запрос списка товаров
        $productListQuery = new Query\Product\GetListByIdList(array_unique(array_merge($alsoBoughtIdList, $similarIdList, $alsoViewedIdList)), $region);
        $curl->prepare($productListQuery);

        $curl->execute(1, 2);

        // товары
        $productsById = $productRepository->getIndexedObjectListByQueryList([$productListQuery]);
    }
}