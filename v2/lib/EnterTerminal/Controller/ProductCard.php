<?php

namespace EnterTerminal\Controller;

use Enter\Http;
use EnterTerminal\ConfigTrait;
use EnterSite\CurlClientTrait;
use EnterSite\Controller;
use EnterSite\Repository;
use EnterSite\Curl\Query;
use EnterSite\Model;
use EnterTerminal\Model\Page\ProductCard as Page;

class ProductCard {
    use ConfigTrait, CurlClientTrait {
        ConfigTrait::getConfig insteadof CurlClientTrait;
    }

    /**
     * @param Http\Request $request
     * @throws \Exception
     * @return Http\JsonResponse
     */
    public function execute(Http\Request $request) {
        $config = $this->getConfig();
        $curl = $this->getCurlClient();
        $productRepository = new Repository\Product();

        // ид магазина
        $shopId = (new \EnterTerminal\Repository\Shop())->getIdByHttpRequest($request); // FIXME

        // ид товара
        $productId = trim((string)$request->query['productId']);
        if (!$productId) {
            throw new \Exception('Не указан параметр productId');
        }

        // запрос магазина
        $shopItemQuery = new Query\Shop\GetItemById($shopId);
        $curl->prepare($shopItemQuery);

        $curl->execute();

        // магазин
        $shop = (new Repository\Shop())->getObjectByQuery($shopItemQuery);
        if (!$shop) {
            throw new \Exception(sprintf('Магазин #%s не найден', $shopId));
        }

        // запрос товара
        $productItemQuery = new Query\Product\GetItemById($productId, $shop->regionId);
        $curl->prepare($productItemQuery);

        $curl->execute();

        // товар
        $product = $productRepository->getObjectByQuery($productItemQuery);
        if (!$product) {
            return (new Controller\Error\NotFound())->execute($request, sprintf('Товар #%s не найден', $productId));
        }

        // запрос доставки товара
        $deliveryListQuery = null;
        if ($product->isBuyable) {
            $deliveryListQuery = new Query\Product\Delivery\GetListByCartProductList([new Model\Cart\Product(['id' => $product->id, 'quantity' => 1])], $shop->regionId);
            $curl->prepare($deliveryListQuery);
        }

        // запрос отзывов товара
        $reviewListQuery = null;
        if ($config->productReview->enabled) {
            $reviewListQuery = new Query\Product\Review\GetListByProductId($product->id, 0, $config->productReview->itemsInCard);
            $curl->prepare($reviewListQuery);
        }

        // запрос видео товара
        $videoListQuery = new Query\Product\Media\Video\GetListByProductId($product->id);
        $curl->prepare($videoListQuery);

        // запрос аксессуаров товара
        $accessoryListQuery = null;
        if ((bool)$product->accessoryIds) {
            $accessoryListQuery = new Query\Product\GetListByIdList(array_slice($product->accessoryIds, 0, $config->product->itemsInSlider), $shop->regionId);
            $curl->prepare($accessoryListQuery);
        }

        // запрос списка рейтингов товаров
        $ratingListQuery = null;
        if ($config->productReview->enabled) {
            $ratingListQuery = new Query\Product\Rating\GetListByProductIdList(array_merge([$product->id], (bool)$product->accessoryIds ? $product->accessoryIds : []));
            $curl->prepare($ratingListQuery);
        }

        $curl->execute();

        // отзывы товара
        $reviews = $reviewListQuery ? (new Repository\Product\Review())->getObjectListByQuery($reviewListQuery) : [];

        // видео товара
        $productRepository->setVideoForObjectByQuery($product, $videoListQuery);
        // 3d фото товара (maybe3d)
        $productRepository->setPhoto3dForObjectByQuery($product, $videoListQuery);

        // доставка товара
        if ($deliveryListQuery) {
            $productRepository->setDeliveryForObjectListByQuery([$product->id => $product], $deliveryListQuery);
        }

        // список магазинов, в которых товар может быть только в магазине
        $shopsIds = [];
        foreach ($product->stock as $stock) {
            if ($stock->shopId && ($stock->quantity > 0)) {
                $shopsIds[] = $stock->shopId;
            }
        }
        if ((bool)$shopsIds) {
            $shopListQuery = new Query\Shop\GetListByIdList($shopsIds);
            $curl->prepare($shopListQuery)->execute();

            $productRepository->setNowDeliveryForObjectListByQuery([$product->id => $product], $shopListQuery);
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

        // страница
        $page = new Page();
        $page->product = $product;
        $page->reviews = $reviews;

        return new Http\JsonResponse($page);
    }
}