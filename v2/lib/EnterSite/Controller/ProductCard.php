<?php

namespace EnterSite\Controller;

use Enter\Http;
use EnterSite\ConfigTrait;
use EnterSite\CurlClientTrait;
use EnterSite\MustacheRendererTrait;
use EnterSite\DebugContainerTrait;
use EnterSite\Controller;
use EnterSite\Repository;
use EnterSite\Curl\Query;
use EnterSite\Model;
use EnterSite\Model\Page\ProductCard as Page;

class ProductCard {
    use ConfigTrait, CurlClientTrait, MustacheRendererTrait, DebugContainerTrait {
        ConfigTrait::getConfig insteadof CurlClientTrait, MustacheRendererTrait, DebugContainerTrait;
    }

    /**
     * @param Http\Request $request
     * @return Http\Response
     */
    public function execute(Http\Request $request) {
        $config = $this->getConfig();
        $curl = $this->getCurlClient();
        $productRepository = new Repository\Product();

        // ид региона
        $regionId = (new Repository\Region())->getIdByHttpRequestCookie($request);

        // токен товара
        $productToken = $productRepository->getTokenByHttpRequest($request);

        // запрос региона
        $regionQuery = new Query\Region\GetItemById($regionId);
        $curl->prepare($regionQuery);

        $curl->execute();

        // регион
        $region = (new Repository\Region())->getObjectByQuery($regionQuery);

        // запрос товара
        $productItemQuery = new Query\Product\GetItemByToken($productToken, $region->id);
        $curl->prepare($productItemQuery);

        $curl->execute();

        // товар
        $product = $productRepository->getObjectByQuery($productItemQuery);
        if (!$product) {
            return (new Controller\Error\NotFound())->execute($request, sprintf('Товар @%s не найден', $productToken));
        }
        if ($product->link !== $request->getPathInfo()) {
            return (new Controller\Redirect())->execute($product->link. ((bool)$request->getQueryString() ? ('?' . $request->getQueryString()) : ''), Http\Response::STATUS_MOVED_PERMANENTLY);
        }

        // запрос дерева категорий для меню
        $categoryListQuery = new Query\Product\Category\GetTreeList($region->id, 3);
        $curl->prepare($categoryListQuery);

        // запрос меню
        $mainMenuQuery = new Query\MainMenu\GetItem();
        $curl->prepare($mainMenuQuery);

        // запрос доставки товара
        $deliveryListQuery = null;
        if ($product->isBuyable) {
            $deliveryListQuery = new Query\Product\Delivery\GetListByCartProductList([new Model\Cart\Product(['id' => $product->id, 'quantity' => 1])], $region->id);
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
            $accessoryListQuery = new Query\Product\GetListByIdList(array_slice($product->accessoryIds, 0, $config->product->itemsInSlider), $region->id);
            $curl->prepare($accessoryListQuery);
        }

        // запрос списка рейтингов товаров
        $ratingListQuery = null;
        if ($config->productReview->enabled) {
            $ratingListQuery = new Query\Product\Rating\GetListByProductIdList(array_merge([$product->id], (bool)$product->accessoryIds ? $product->accessoryIds : []));
            $curl->prepare($ratingListQuery);
        }

        // TODO: загрузка предков категории как в каталоге

        // запрос настроек каталога
        $catalogConfigQuery = new Query\Product\Catalog\Config\GetItemByProductCategoryObject(array_merge($product->category ? $product->category->ascendants : [], [$product->category]), $product);
        $curl->prepare($catalogConfigQuery);

        $curl->execute();

        // меню
        $mainMenu = (new Repository\MainMenu())->getObjectByQuery($mainMenuQuery, $categoryListQuery);

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

        // если у товара нет доставок, запрашиваем список магазинов, в которых товар может быть на витрине
        // FIXME: временно недоступно
        if (false && !(bool)$product->nearestDeliveries) {
            $shopsIds = [];
            foreach ($product->stock as $stock) {
                if ($stock->shopId && ($stock->showroomQuantity > 0)) {
                    $shopsIds[] = $stock->shopId;
                }
            }

            if ((bool)$shopsIds) {
                $shopListQuery = new Query\Shop\GetListByIdList($shopsIds);
                $curl->prepare($shopListQuery);

                $curl->execute();

                $productRepository->setNowDeliveryForObjectListByQuery([$product->id => $product], $shopListQuery);
            }
        }

        // настройки каталога
        $catalogConfig = (new Repository\Product\Catalog\Config())->getObjectByQuery($catalogConfigQuery);

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

        // категории аксессуаров
        $accessoryCategories = (new Repository\Product\Category())->getIndexedObjectListByProductListAndTokenList($product->relation->accessories, $catalogConfig->accessoryCategoryTokens);

        // запрос для получения страницы
        $pageRequest = new Repository\Page\ProductCard\Request();
        $pageRequest->region = $region;
        $pageRequest->mainMenu = $mainMenu;
        $pageRequest->product = $product;
        $pageRequest->accessoryCategories = $accessoryCategories;
        $pageRequest->reviews = $reviews;
        //die(json_encode($pageRequest, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        // страница
        $page = new Page();
        (new Repository\Page\ProductCard())->buildObjectByRequest($page, $pageRequest);

        // debug
        if ($config->debugLevel) $this->getDebugContainer()->page = $page;
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