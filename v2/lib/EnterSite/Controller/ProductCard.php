<?php

namespace EnterSite\Controller;

use Enter\Exception;
use Enter\Http;
use EnterSite\ConfigTrait;
use EnterSite\CurlClientTrait;
use EnterSite\MustacheRendererTrait;
use EnterSite\Repository;
use EnterSite\Curl\Query;
use EnterSite\Model;
//use EnterSite\Model\Page\ProductCard as Page;

class ProductCard {
    use ConfigTrait;
    use CurlClientTrait, MustacheRendererTrait {
        ConfigTrait::getConfig insteadof CurlClientTrait;
        ConfigTrait::getConfig insteadof MustacheRendererTrait;
    }

    public function execute(Http\Request $request) {
        $config = $this->getConfig();
        $curl = $this->getCurlClient();
        $productRepository = new Repository\Product();

        // ид региона
        $regionId = (new Repository\Region())->getIdByHttpRequest($request);

        // токен товара
        $productToken = (new Repository\Product)->getTokenByHttpRequest($request);

        // запрос региона
        $regionQuery = new Query\Region\GetItemById($regionId);
        $curl->prepare($regionQuery);

        $curl->execute(1, 2);

        // регион
        $region = (new Repository\Region())->getObjectByQuery($regionQuery);

        // запрос товара
        $productItemQuery = new Query\Product\GetItemByToken($productToken, $region);
        $curl->prepare($productItemQuery);

        $curl->execute(1, 2);

        // товар
        $product = $productRepository->getObjectByQuery($productItemQuery);
        if ($product->link !== $request->getPathInfo()) {
            $redirect = new Exception\PermanentlyRedirect();
            $redirect->setLink($product->link. ((bool)$request->getQueryString() ? ('?' . $request->getQueryString()) : ''));

            throw $redirect;
        }

        // запрос доставки товара
        $deliveryListQuery = null;
        if ($product->isBuyable) {
            $deliveryListQuery = new Query\Product\Delivery\GetListByCartProductList([new Model\Cart\Product(['id' => $product->id, 'quantity' => 1])], $region);
            $curl->prepare($deliveryListQuery);
        }

        // запрос отзывов товара
        $reviewListQuery = null;
        if ($config->productReview->enabled) {
            $reviewListQuery = new Query\Product\Review\GetListByProductId($product->id, 0, $config->productReview->itemsInCard);
            $curl->prepare($reviewListQuery);
        }

        // запрос ведео товара
        $videoListQuery = new Query\Product\Media\Video\GetListByProductId($product->id);
        $curl->prepare($videoListQuery);

        // запрос аксессуаров товара
        // TODO: группировка аксессуаров по категориям
        $accessoryListQuery = new Query\Product\GetListByIdList(array_slice($product->accessoryIds, 0, $config->product->itemsInSlider), $region);
        $curl->prepare($accessoryListQuery);

        $curl->execute(1, 2);

        // отзывы товара
        $reviews = $reviewListQuery ? (new Repository\Product\Review())->getObjectListByQuery($reviewListQuery) : [];

        // видео товара
        $productRepository->setVideoForObjectByQuery($product, $videoListQuery);

        // доставка товара
        if ($deliveryListQuery) {
            $productRepository->setDeliveryForObjectListByQuery([$product->id => $product], $deliveryListQuery);
        }

        // если у товара нет доставок, запрашиваем список магазинов, в которых товар может быть на витрине
        if (!(bool)$product->nearestDeliveries) {
            $shopsIds = [];
            foreach ($product->stock as $stock) {
                if ($stock->shopId && ($stock->showroomQuantity > 0)) {
                    $shopsIds[] = $stock->shopId;
                }
            }

            if ((bool)$shopsIds) {
                $shopListQuery = new Query\Shop\GetListByIdList($shopsIds);
                $curl->prepare($shopListQuery);

                $curl->execute(1, 2);

                $productRepository->setShowroomDeliveryForObjectListByQuery([$product->id => $product], $shopListQuery);
            }
        }

        // аксессуары
        $productRepository->setAccessoryRelationForObjectListByQuery([$product->id => $product], $accessoryListQuery);

        die(var_dump($product));
    }
}