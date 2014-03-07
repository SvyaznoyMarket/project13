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
use EnterSite\Model\Page\ProductCard as Page;

class ProductCard {
    use ConfigTrait;
    use CurlClientTrait, MustacheRendererTrait {
        ConfigTrait::getConfig insteadof CurlClientTrait, MustacheRendererTrait;
    }

    /**
     * @param Http\Request $request
     * @return Http\Response
     * @throws \Enter\Exception\PermanentlyRedirect
     */
    public function execute(Http\Request $request) {
        $config = $this->getConfig();
        $curl = $this->getCurlClient();
        $productRepository = new Repository\Product();

        // ид региона
        $regionId = (new Repository\Region())->getIdByHttpRequest($request);

        // токен товара
        $productToken = $productRepository->getTokenByHttpRequest($request);

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
        $accessoryListQuery = null;
        if ((bool)$product->accessoryIds) {
            $accessoryListQuery = new Query\Product\GetListByIdList(array_slice($product->accessoryIds, 0, $config->product->itemsInSlider), $region);
            $curl->prepare($accessoryListQuery);
        }

        // запрос списка рейтингов товаров
        $ratingListQuery = null;
        if ($config->productReview->enabled) {
            $ratingListQuery = new Query\Product\Rating\GetListByProductIdList(array_merge([$product->id], $product->accessoryIds));
            $curl->prepare($ratingListQuery);
        }

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
        if ($accessoryListQuery) {
            $productRepository->setAccessoryRelationForObjectListByQuery([$product->id => $product], $accessoryListQuery);
        }

        // группированные товары
        $productsById = [];
        foreach (array_merge([$product], $product->relation->accessories) as $iProduct) {
            /** @var Model\Product $iProduct */
            $productsById[$iProduct->id] = $iProduct;
        }

        // список рейтингов товаров
        if ($ratingListQuery) {
            $productRepository->setRatingForObjectListByQuery($productsById, $ratingListQuery);
        }

        // запрос для получения страницы
        $pageRequest = new Repository\Page\ProductCard\Request();
        $pageRequest->region = $region;
        $pageRequest->product = $product;

        // страница
        $page = new Page();
        (new Repository\Page\ProductCard())->buildObjectByRequest($page, $pageRequest);
        //die(json_encode($page, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        // рендер
        $renderer = $this->getRenderer();
        $renderer->setPartials([
            'content' => 'page/product-card/content',
        ]);
        $content = $renderer->render('layout/default', $page);

        // http-ответ
        $response = new Http\Response($content);

        return $response;
    }
}