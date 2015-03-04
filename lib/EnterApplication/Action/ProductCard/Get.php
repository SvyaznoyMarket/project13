<?php

namespace EnterApplication\Action\ProductCard {

    use EnterApplication\Action\ProductCard\Get\Request;
    use EnterQuery as Query;

    class Get {
        use \EnterApplication\CurlTrait;

        public function execute(Request $request)
        {
            $startAt = microtime(true);

            $curl = $this->getCurl();

            // товар
            $productQuery = null;
            if ($request->productCriteria['token']) {
                $productQuery = new Query\Product\GetByToken($request->productCriteria['token'], $request->regionId);
            }
            if (!$productQuery) {
                throw new \InvalidArgumentException('Неверный критерий получения товара');
            }

            // доставка, группы оплаты, магазины, отзывы и рейтинг товаров, ...
            $productQuery->prepare($productError, [
                // доставка
                function() use (&$productQuery, &$deliveryQuery) {
                    $product = $productQuery->response->product;
                    if (!$product['id']) return;

                    $deliveryQuery = new Query\Delivery\GetByCart();
                    // корзина
                    $deliveryQuery->cart->products[] = $deliveryQuery->cart->createProduct($product['id'], 1);
                    // регион
                    $deliveryQuery->regionId = $productQuery->regionId;

                    $deliveryQuery->prepare($deliveryError);
                    /*
                    $deliveryQuery->prepare($deliveryError, function() use (&$deliveryQuery, &$deliveryError) {
                        if ($deliveryError || !$deliveryQuery->response->shops) return;

                        $shopQuery = (new Query\Shop\GetById(array_keys($deliveryQuery->response->shops)))->prepare($shopError);
                    });
                    */
                },

                // магазины на основе остатков
                function() use (&$productQuery, &$shopQuery) {
                    $product = $productQuery->response->product;
                    if (!$product['id']) return;

                    $shopQuery = new Query\Shop\GetById();
                    foreach ($product['stock'] as $stock) {
                        if (!$stock['shop_id'] || !($stock['quantity'] + $stock['quantity_showroom'])) continue;

                        $shopQuery->ids[] = $stock['shop_id'];
                    }
                    if ($shopQuery->ids) {
                        $shopQuery->prepare($shopError);
                    }
                },

                // группы оплаты
                function() use (&$productQuery, &$paymentGroupQuery) {
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
                },

                // рейтинг товаров
                function() use (&$productQuery, &$ratingQuery) {
                    $ratingQuery = null;

                    if ($accessoryIds = array_slice((array)$productQuery->response->product['accessories'], 0, \App::config()->product['itemsPerPage'])) {
                        $ratingQuery = new Query\Product\Review\GetScoreByProductIdList();
                        $ratingQuery->productIds = array_merge($ratingQuery->productIds, $accessoryIds);
                    }

                    if ($ratingQuery) {
                        $ratingQuery->prepare($ratingError);
                    }
                },

                // отзывы товара
                function() use (&$productQuery, &$reviewQuery) {
                    $product = $productQuery->response->product;
                    if (!$product['id']) return;

                    $reviewQuery = (new Query\Product\Review\GetByProductUi($product['ui'], 0, 7))->prepare($reviewError);
                },

                // категория товаров
                function() use (&$productQuery, &$categoryQuery) {
                    $product = $productQuery->response->product;
                    if (!$product['id']) return;

                    $categoryQuery = null;
                    if ($categoryUi = end($product['category'])['ui']) {
                        $categoryQuery = (new Query\Product\Category\GetByUi($categoryUi, $productQuery->regionId))->prepare($categoryError);
                    }
                },

                function() use (&$productQuery, &$productDescriptionQuery) {
                    $product = $productQuery->response->product;
                    if (!$product['id']) return;

                    // описание товара из scms
                    $productDescriptionQuery = (new Query\Product\GetDescriptionByUi([$product['ui']]))->prepare($productDescriptionError);
                },
            ]);

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
            if ($request->userToken) {
                $userQuery = (new Query\User\GetByToken($request->userToken))->prepare($userError);
                $subscribeQuery = (new Query\Subscribe\GetByUserToken($request->userToken))->prepare($subscribeError);
            }

            // редирект
            $redirectQuery = (new Query\Redirect\GetByUrl($request->urlPath))->prepare($redirectError); // TODO: throw Exception

            // аб-тест
            $abTestQuery = (new Query\AbTest\GetActive())->prepare($abTestError);

            // регион
            $regionQuery = (new Query\Region\GetById($request->regionId))->prepare($abTestError);

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

            //die(microtime(true) - $startAt);
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

namespace EnterApplication\Action\ProductCard\Get {
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
}