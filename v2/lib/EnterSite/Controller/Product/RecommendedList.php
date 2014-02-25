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

        // запрос "с этим товаром также покупают" товаров
        $crossSellItemToItemsListQuery = new Query\Product\Relation\CrossSellItemToItems\GetIdListByProductId($product->id);
        $curl->prepare($crossSellItemToItemsListQuery);

        $curl->execute(1, 2);

        $alsoBoughtIdList = $product->relatedIds;
        try {
            $alsoBoughtIdList = array_unique(array_merge($alsoBoughtIdList, $crossSellItemToItemsListQuery->getResult()));
        } catch (\Exception $e) {
            $logger->push(['type' => 'error', 'error' => $e, 'action' => __METHOD__, 'tag' => ['product.recommendation']]);
        }
    }
}