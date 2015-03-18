<?php

namespace EnterApplication\Action\ProductCard
{
    use EnterApplication\Action\ProductCard\Get\Request;
    use EnterApplication\Action\ProductCard\Get\Response;
    use EnterQuery as Query;

    class Get {
        use \EnterApplication\CurlTrait;

        /**
         * @param Request $request
         * @return Response
         */
        public function execute(Request $request)
        {
            $startAt = microtime(true);
            $GLOBALS['startAt'] = $startAt;

            $curl = $this->getCurl();

            /*
            register_shutdown_function(function() use ($startAt) {
                var_dump((round((microtime(true) - $GLOBALS['startAt']) * 1000)) . ' | ' . round(memory_get_peak_usage() / 1048576, 2) . ' Mb');
                var_dump((round((microtime(true) - $GLOBALS['startAt']) * 1000)) . ' | ' . (microtime(true) - $startAt));
            });

            $abTestQuery = (new Query\AbTest\GetActive())->prepare($abTestError);
            $regionQuery = (new Query\Region\GetById($request->regionId))->prepare($regionError);
            $curl->execute();
            die();
            */

            // товар
            $productQuery = null;
            if ($request->productCriteria['token']) {
                $productQuery = new Query\Product\GetByToken($request->productCriteria['token'], $request->regionId);
            }
            if (!$productQuery) {
                throw new \InvalidArgumentException('Неверный критерий получения товара');
            }

            // доставка
            $deliveryCallback = $productQuery->addCallback(function() use (&$productQuery, &$deliveryQuery) {
                $product = $productQuery->response->product;
                if (!$product['id']) return;

                $deliveryQuery = new Query\Delivery\GetByCart();
                // корзина
                $deliveryQuery->cart->products[] = $deliveryQuery->cart->createProduct($product['id'], 1);
                foreach(array_column($productQuery->response->product['kit'], 'id') as $kitId) {
                    $deliveryQuery->cart->products[] = $deliveryQuery->cart->createProduct($kitId, 1);
                }

                // регион
                $deliveryQuery->regionId = $productQuery->regionId;

                $deliveryQuery->prepare($deliveryError);
                /*
                $deliveryQuery->prepare($deliveryError, function() use (&$deliveryQuery, &$deliveryError) {
                    if ($deliveryError || !$deliveryQuery->response->shops) return;

                    $shopQuery = (new Query\Shop\GetById(array_keys($deliveryQuery->response->shops)))->prepare($shopError);
                });
                */
            });

            // магазины на основе остатков
            $shopCallback = $productQuery->addCallback(function() use (&$productQuery, &$shopQuery) {
                $product = $productQuery->response->product;
                if (!$product['id']) return;

                $shopQuery = new Query\Shop\GetByIdList();
                foreach ($product['stock'] as $stock) {
                    if (!$stock['shop_id'] || !($stock['quantity'] + $stock['quantity_showroom'])) continue;

                    $shopQuery->ids[] = $stock['shop_id'];
                }
                if ($shopQuery->ids) {
                    $shopQuery->prepare($shopError);
                }
            });

            // группы оплаты
            $paymentGroupCallback = $productQuery->addCallback(function() use (&$productQuery, &$paymentGroupQuery) {
                $product = $productQuery->response->product;
                if (!$product['id']) return;

                $cart = \App::user()->getCart(); // TODO: old usage

                $paymentGroupQuery = new Query\PaymentGroup\GetByCart();
                // корзина
                $paymentGroupQuery->cart->products[] = $paymentGroupQuery->cart->createProduct($product['id'], 1);
                // регион
                $paymentGroupQuery->regionId = $productQuery->regionId;
                // фильтер
                $paymentGroupQuery->filter->isCorporative = false;
                $paymentGroupQuery->filter->isCredit = (bool)(($product['price'] * (($cart->getQuantityByProduct($product['id']) > 0) ? $cart->getQuantityByProduct($product['id']) : 1)) >= \App::config()->product['minCreditPrice']);

                $paymentGroupQuery->prepare($paymentGroupError);
            });

            // рейтинг товаров
            $ratingCallback = $productQuery->addCallback(function() use (&$productQuery, &$ratingQuery) {
                $ids = []; // идентификаторы товаров
                if ($accessoryIds = array_slice((array)$productQuery->response->product['accessories'], 0, \App::config()->product['itemsPerPage'])) {
                    $ids = array_merge($ids, $accessoryIds);
                }

                if ($kitIds = array_column($productQuery->response->product['kit'], 'id')) {
                    $ids = array_merge($ids, $kitIds);
                }

                if ($ids) {
                    $ratingQuery = new Query\Product\Review\GetScoreByProductIdList($ids);
                    $ratingQuery->prepare($ratingError);
                }
            });

            // связанные товары: аксессуары, наборы, ...
            $relatedProductCallback = $productQuery->addCallback(function() use (&$productQuery, &$relatedProductQueries) {
                $ids = []; // идентификаторы товаров
                if ($accessoryIds = array_slice((array)$productQuery->response->product['accessories'], 0, \App::config()->product['itemsPerPage'])) {
                    $ids = array_merge($ids, $accessoryIds);
                }

                if ($kitIds = array_column($productQuery->response->product['kit'], 'id')) {
                    $ids = array_merge($ids, $kitIds);
                }

                if ($ids) {
                    $relatedProductQueries = [];
                    foreach (array_chunk($ids, \App::config()->coreV2['chunk_size']) as $idsInChunk) {
                        $relatedProductQueries[] = (new Query\Product\GetByIdList($idsInChunk, $productQuery->regionId))->prepare($relatedProductError);
                    }
                }
            });

            // отзывы товара
            $reviewCallback = $productQuery->addCallback(function() use (&$productQuery, &$reviewQuery) {
                $product = $productQuery->response->product;
                if (!$product['id']) return;

                $reviewQuery = (new Query\Product\Review\GetByProductUi($product['ui'], 0, 7))->prepare($reviewError);
            });

            // категория товаров
            $categoryCallback = $productQuery->addCallback(function() use (&$productQuery, &$categoryQuery) {
                $product = $productQuery->response->product;
                if (!$product['id']) return;

                $categoryQuery = null;
                if ($categoryUi = end($product['category'])['ui']) {
                    $categoryQuery = (new Query\Product\Category\GetByUi($categoryUi, $productQuery->regionId))->prepare($categoryError);
                }
            });

            // описание товара из scms
            $productDescriptionCallback = $productQuery->addCallback(function() use (&$productQuery, &$productDescriptionQuery) {
                $product = $productQuery->response->product;
                if (!$product['id']) return;

                $productDescriptionQuery = (new Query\Product\GetDescriptionByUiList([$product['ui']]))->prepare($productDescriptionError);
            });

            // товар для Подари Жизнь
            $lifeGiftProductCallback = $productQuery->addCallback(function() use (&$productQuery, &$lifeGiftProductQuery) {
                $product = $productQuery->response->product;
                if (!$product['ui']) return;

                $labelId = isset($product['label'][0]['id']) ? $product['label'][0]['id'] : null;
                if (
                    \App::config()->lifeGift['enabled']
                    && $labelId
                    && (\App::config()->lifeGift['labelId'] === $labelId)
                ) {
                    $lifeGiftProductQuery = new Query\Product\GetByUi($product['ui'], \App::config()->lifeGift['regionId']);
                }
            });

            // подготовка запроса на получение товара
            $productQuery->prepare($productError);

            // регион
            $regionQuery = (new Query\Region\GetById($request->regionId))->prepare($regionError);

            $curl->execute(); // важно: только товар и регион в запросе

            // отзывы о товаре
            /*
            $reviewQuery = null;
            if ($request->productCriteria['token']) {
                $reviewQuery = new Query\Product\Review\GetByProductToken($request->productCriteria['token'], 0, 7);
            }
            if ($reviewQuery) {
                $reviewQuery->prepare($reviewError);
            }
            */

            // пользователь и его подписки
            $userQuery = null;
            $subscribeQuery = null;
            if ($request->userToken) {
                $userQuery = (new Query\User\GetByToken($request->userToken))->prepare($userError);
                $subscribeQuery = (new Query\Subscribe\GetByUserToken($request->userToken))->prepare($subscribeError);
            }

            // редирект
            $redirectQuery = (new Query\Redirect\GetByUrl($request->urlPath))->prepare($redirectError); // TODO: throw Exception

            // аб-тест
            $abTestQuery = (new Query\AbTest\GetActive())->prepare($abTestError);

            // список регионов для выбора города
            $mainRegionQuery = (new Query\Region\GetMain())->prepare($mainRegionError);

            // каналы подписок
            $subscribeChannelQuery = (new Query\Subscribe\Channel\Get())->prepare($subscribeChannelError);

            // дерево категорий для меню
            //$categoryTreeQuery = (new Query\Product\Category\GetTree(null, 3, null, null, true))->prepare($categoryTreeError);
            $categoryRootTreeQuery = (new Query\Product\Category\GetRootTree($request->regionId, 3))->prepare($categoryRootTreeError);

            // главное меню
            $menuQuery = (new Query\MainMenu\GetByTagList(['site-web']))->prepare($menuError);

            // выполнение запросов
            $curl->execute();

            $this->removeCurl();

            // response
            $response = new Response();
            $response->productQuery = $productQuery;
            $response->productDescriptionQuery = $productDescriptionQuery;
            $response->userQuery = $userQuery;
            $response->subscribeQuery = $subscribeQuery;
            $response->redirectQuery = $redirectQuery;
            $response->abTestQuery = $abTestQuery;
            $response->regionQuery = $regionQuery;
            $response->mainRegionQuery = $mainRegionQuery;
            $response->subscribeChannelQuery = $subscribeChannelQuery;
            $response->categoryRootTreeQuery = $categoryRootTreeQuery;
            $response->menuQuery = $menuQuery;
            $response->deliveryQuery = $deliveryQuery;
            $response->shopQuery = $shopQuery;
            $response->paymentGroupQuery = $paymentGroupQuery;
            $response->ratingQuery = $ratingQuery;
            $response->relatedProductQueries = $relatedProductQueries;
            $response->reviewQuery = $reviewQuery;
            $response->categoryQuery = $categoryQuery;
            $response->lifeGiftProductQuery = $lifeGiftProductQuery;

            //var_dump($GLOBALS['enter/curl/query/cache']);
            //var_dump($response);
            //die(var_dump('done'));

            return $response;
        }

        /**
         * @return Request
         */
        public function createRequest()
        {
            return new Request();
        }
    }
}

namespace EnterApplication\Action\ProductCard\Get
{
    use EnterQuery as Query;

    class Request
    {
        /** @var string */
        public $urlPath;
        /** @var array */
        public $productCriteria;
        /** @var string */
        public $regionId;
        /** @var string|null */
        public $userToken;
    }

    class Response
    {
        /** @var Query\Product\GetByToken */
        public $productQuery;
        /** @var Query\Product\GetDescriptionByUiList|null */
        public $productDescriptionQuery;
        /** @var Query\User\GetByToken|null */
        public $userQuery;
        /** @var Query\Subscribe\GetByUserToken|null */
        public $subscribeQuery;
        /** @var Query\Redirect\GetByUrl */
        public $redirectQuery;
        /** @var Query\AbTest\GetActive */
        public $abTestQuery;
        /** @var Query\Region\GetById */
        public $regionQuery;
        /** @var Query\Region\GetMain */
        public $mainRegionQuery; // TODO: убрать, будет через ajax
        /** @var Query\Subscribe\Channel\Get */
        public $subscribeChannelQuery;
        /** @var Query\Product\Category\GetRootTree */
        public $categoryRootTreeQuery;
        /** @var Query\MainMenu\GetByTagList */
        public $menuQuery;
        /** @var Query\Delivery\GetByCart|null */
        public $deliveryQuery;
        /** @var Query\Shop\GetByIdList|null */
        public $shopQuery;
        /** @var Query\PaymentGroup\GetByCart|null */
        public $paymentGroupQuery;
        /** @var Query\Product\Review\GetScoreByProductIdList|null */
        public $ratingQuery;
        /** @var Query\Product\GetByIdList[] */
        public $relatedProductQueries = [];
        /** @var Query\Product\Review\GetByProductUi|null */
        public $reviewQuery;
        /** @var Query\Product\Category\GetByUi|null */
        public $categoryQuery;
        /** @var Query\Product\GetByToken */
        public $lifeGiftProductQuery;
    }
}