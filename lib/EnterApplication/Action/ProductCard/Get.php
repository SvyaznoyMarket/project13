<?php

namespace EnterApplication\Action\ProductCard
{
    use EnterApplication\Action\ProductCard\Get\Request;
    use EnterApplication\Action\ProductCard\Get\Response;
    use EnterQuery as Query;

    class Get {
        use \EnterApplication\CurlTrait;
        use \EnterApplication\Action\ActionTrait;

        /**
         * @param Request $request
         * @return Response
         */
        public function execute(Request $request)
        {
            //$startAt = microtime(true);
            //$GLOBALS['startAt'] = $startAt;
            \Debug\Timer::start('curl');

            $curl = $this->getCurl();

            // регион
            $regionQuery = $this->getRegionQuery($request->regionId);

            // редирект
            $redirectQuery = (new Query\Redirect\GetByUrl($request->urlPath))->prepare(); // TODO: throw Exception

            // аб-тест
            $abTestQuery = (new Query\AbTest\GetActive())->prepare();

            // главное меню
            $menuQuery = (new Query\MainMenu\GetByTagList(['site-web']))->prepare();

            // выполнение запросов
            $curl->execute();

            // проверка региона
            $this->checkRegionQuery($regionQuery);

            // товар
            $productQuery = null;
            if ($request->productCriteria['token']) {
                $productQuery = new Query\Product\GetByToken($request->productCriteria['token'], $regionQuery->response->region['id']);
            }
            if (!$productQuery) {
                throw new \InvalidArgumentException('Неверный критерий получения товара');
            }
            // подготовка запроса на получение товара
            $productQuery->prepare();

            // дерево категорий для меню
            //$categoryTreeQuery = (new Query\Product\Category\GetTree(null, 3, null, null, true))->prepare($categoryTreeError);
            $categoryRootTreeQuery = (new Query\Product\Category\GetRootTree($regionQuery->response->region['id'], 3))->prepare();

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
            /** @var Query\User\GetByToken $userQuery */
            $userQuery = null;
            if ($request->userToken) {
                // пользователь
                $userQuery = (new Query\User\GetByToken($request->userToken))->prepare();
            }

            // каналы подписок
            $subscribeChannelQuery = (new Query\Subscribe\Channel\Get())->prepare();

            // выполнение запросов
            $curl->execute();

            call_user_func(function() use (&$productQuery, &$userQuery, &$productViewEventQuery) {
                $productUi = $productQuery->response->product['ui'];
                if (!$productUi || !$userQuery || !\App::config()->eventService['enabled']) return;

                // product view событие
                $productViewEventQuery = (new Query\Event\PushProductView($productUi, $userQuery->response->user['ui']))->prepare();
            });

            // доставка
            call_user_func(function() use (&$productQuery, &$deliveryQuery) {
                $productId = $productQuery->response->product['id'];
                if (!$productId) return;

                $deliveryQuery = new Query\Delivery\GetByCart();
                // корзина
                $deliveryQuery->cart->products[] = $deliveryQuery->cart->createProduct($productId, 1);
                foreach(array_column($productQuery->response->product['kit'], 'id') as $kitId) {
                    $deliveryQuery->cart->products[] = $deliveryQuery->cart->createProduct($kitId, 1);
                }

                // регион
                $deliveryQuery->regionId = $productQuery->regionId;

                $deliveryQuery->prepare();
                /*
                $deliveryQuery->prepare($deliveryError, function() use (&$deliveryQuery, &$deliveryError) {
                    if ($deliveryError || !$deliveryQuery->response->shops) return;

                    $shopQuery = (new Query\Shop\GetById(array_keys($deliveryQuery->response->shops)))->prepare($shopError);
                });
                */
            });

            // магазины на основе остатков
            call_user_func(function() use (&$productQuery, &$shopQuery) {
                $shopIds = [];
                foreach ($productQuery->response->product['stock'] as $stock) {
                    if (!$stock['shop_id'] || !($stock['quantity'] + $stock['quantity_showroom'])) continue;

                    $shopIds[] = $stock['shop_id'];
                }
                if ($shopIds) {
                    $shopQuery = (new Query\Shop\GetByIdList($shopIds))->prepare();
                }
            });

            // группы оплаты
            call_user_func(function() use (&$productQuery, &$paymentGroupQuery) {
                return false; // SITE-5460

                $productId = $productQuery->response->product['id'];
                if (!$productId) return;

                $price = $productQuery->response->product['price'];

                $cart = \App::user()->getCart(); // TODO: old usage

                $paymentGroupQuery = new Query\PaymentGroup\GetByCart();
                // корзина
                $paymentGroupQuery->cart->products[] = $paymentGroupQuery->cart->createProduct($productId, 1);
                // регион
                $paymentGroupQuery->regionId = $productQuery->regionId;
                // фильтер
                $paymentGroupQuery->filter->isCorporative = false;
                $paymentGroupQuery->filter->isCredit = (bool)(($price * (($cart->getQuantityByProduct($productId) > 0) ? $cart->getQuantityByProduct($productId) : 1)) >= \App::config()->product['minCreditPrice']);

                $paymentGroupQuery->prepare();
            });

            // рейтинг товаров
            call_user_func(function() use (&$productQuery, &$ratingQuery) {
                $ids = []; // идентификаторы товаров
                if ($accessoryIds = array_slice((array)$productQuery->response->product['accessories'], 0, \App::config()->product['itemsPerPage'])) {
                    $ids = array_merge($ids, $accessoryIds);
                }

                if ($kitIds = array_column($productQuery->response->product['kit'], 'id')) {
                    $ids = array_merge($ids, $kitIds);
                }

                if ($ids) {
                    $ratingQuery = new Query\Product\Review\GetScoreByProductIdList($ids);
                    $ratingQuery->prepare();
                }
            });

            // связанные товары: аксессуары, наборы, ...
            /** @var Query\Product\GetByIdList[] $relatedProductQueries */
            $relatedProductQueries = [];
            call_user_func(function() use (&$productQuery, &$relatedProductQueries) {
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
                        $relatedProductQueries[] = (new Query\Product\GetByIdList($idsInChunk, $productQuery->regionId))->prepare();
                    }
                }
            });

            // отзывы товара
            call_user_func(function() use (&$productQuery, &$reviewQuery) {
                $productUi = $productQuery->response->product['ui'];
                if (!$productUi) return;
                $pageSize = \App::abTest()->isNewProductPage() ? 10 : 7;
                $reviewQuery = (new Query\Product\Review\GetByProductUi($productUi, 0, $pageSize))->prepare();
            });

            // категория товаров
            call_user_func(function() use (&$productQuery, &$categoryQuery) {
                $categoryUi = isset($productQuery->response->product['category']) ? end($productQuery->response->product['category'])['ui'] : null;
                if (!$categoryUi) return;

                $categoryQuery = (new Query\Product\Category\GetByUi($categoryUi, $productQuery->regionId))->prepare();
            });

            // избранные товары пользователя
            call_user_func(function() use (&$userQuery, &$productQuery, &$favoriteQuery) {
                $userUi = ($userQuery && $userQuery->response->user['ui']) ? $userQuery->response->user['ui'] : null;
                $productUi = $productQuery->response->product['ui'];

                $favoriteQuery = new Query\User\Favorite\Check($userUi, [$productUi]);
                if ($productUi && $userUi) $favoriteQuery->prepare();
            });

            // товар для Подари Жизнь
            /** @var Query\Product\GetByUi $lifeGiftProductQuery */
            call_user_func(function() use (&$productQuery, &$lifeGiftProductQuery) {
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

            // выполнение запросов
            $curl->execute();

            // описание товара из scms
            call_user_func(function() use (&$productQuery, &$productDescriptionQueries, &$relatedProductQueries, &$lifeGiftProductQuery) {
                $uis = [];
                if (isset($productQuery->response->product['ui'])) {
                    $query = new Query\Product\GetDescriptionByUiList();
                    $query->uis = [$productQuery->response->product['ui']];
                    $query->filter->trustfactor = true;
                    $query->filter->media = true;
                    $query->filter->seo = true;
                    $query->filter->property = true;
                    $query->prepare();

                    $productDescriptionQueries[] = $query;
                }

                foreach ($relatedProductQueries as $relatedProductQuery) {
                    if (is_array($relatedProductQuery->response->products)) {
                        foreach ($relatedProductQuery->response->products as $product) {
                            if (isset($product['ui'])) {
                                $uis[] = $product['ui'];
                            }
                        }
                    }
                }

                if (isset($lifeGiftProductQuery->response->product['ui'])) {
                    $uis[] = $lifeGiftProductQuery->response->product['ui'];
                }

                foreach (array_chunk($uis, \App::config()->coreV2['chunk_size']) as $uisChunk) {
                    $query = new Query\Product\GetDescriptionByUiList();
                    $query->uis = $uisChunk;
                    $query->filter->media = true;
                    $query->prepare();

                    $productDescriptionQueries[] = $query;
                }
            });

            $curl->execute();

            $this->removeCurl();

            // обработка ошибок
            if ($menuQuery->error) {
                $menuQuery->response->items = \App::dataStoreClient()->query('/main-menu.json')['item'];

                \App::logger()->error(['error' => $menuQuery->error, 'sender' => __FILE__ . ' ' .  __LINE__], ['main_menu', 'controller']);
            }

            // response
            $response = new Response();
            $response->productQuery = $productQuery;
            $response->productDescriptionQueries = $productDescriptionQueries;
            $response->userQuery = $userQuery;
            $response->favoriteQuery = $favoriteQuery;
            $response->subscribeQuery = $subscribeQuery;
            $response->redirectQuery = $redirectQuery;
            $response->abTestQuery = $abTestQuery;
            $response->regionQuery = $regionQuery;
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

            \Debug\Timer::stop('curl');

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
        /** @var Query\Product\GetDescriptionByUiList[] */
        public $productDescriptionQueries = [];
        /** @var Query\User\GetByToken|null */
        public $userQuery;
        /** @var Query\Redirect\GetByUrl */
        public $redirectQuery;
        /** @var Query\AbTest\GetActive */
        public $abTestQuery;
        /** @var Query\Region\GetById */
        public $regionQuery;
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
        /** @var Query\User\Favorite\Check|null */
        public $favoriteQuery;
        /** @var Query\Product\GetByToken */
        public $lifeGiftProductQuery;
    }
}