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
            $config = \App::config();
            $curl = $this->getCurl();

            // регион
            $regionQuery =
                ((true === \App::config()->region['cache']) && ($request->regionId === (string)$config->region['defaultId']))
                ? null
                : $this->getRegionQuery($request->regionId)
            ;

            // редирект
            $redirectQuery =
                $config->abTest['enabled']
                ? (new Query\Redirect\GetByUrl($request->urlPath))->prepare()
                : null
            ;

            // аб-тест
            $abTestQuery =
                $config->abTest['enabled']
                ? (new Query\AbTest\GetActive())->prepare()
                : null
            ;

            // главное меню
            /** @var Query\MainMenu\GetByTagList|null $menuQuery */
            $menuQuery =
                ('on' !== \App::request()->headers->get('SSI'))
                ? (new Query\MainMenu\GetByTagList(['site-web']))->prepare()
                : null
            ;

            // выполнение запросов
            $curl->execute();

            // проверка региона
            $this->checkRegionQuery($regionQuery);

            // товар
            /** @var Query\Product\GetByToken|Query\Product\GetByBarcode $productQuery */
            /** @var Query\Product\GetDescriptionByTokenList|Query\Product\GetDescriptionByBarcodeList $productDescriptionQuery */
            /** @var Query\Product\Model\GetByTokenList|Query\Product\Model\GetByBarcodeList $productModelQuery */
            call_user_func(function() use (&$productQuery, &$productDescriptionQuery, &$productModelQuery, $regionQuery, $request, &$config) {
                if ($request->productCriteria['token']) {
                    $productQuery = new Query\Product\GetByToken($request->productCriteria['token'], $regionQuery->response->region['id']);
                    $productQuery->prepare();

                    $productDescriptionQuery = new Query\Product\GetDescriptionByTokenList();
                    $productDescriptionQuery->tokens = [$request->productCriteria['token']];
                    $productDescriptionQuery->filter->trustfactor = true;
                    $productDescriptionQuery->filter->category = true;
                    $productDescriptionQuery->filter->media = true;
                    $productDescriptionQuery->filter->seo = true;
                    $productDescriptionQuery->filter->property = true;
                    $productDescriptionQuery->filter->label = true;
                    $productDescriptionQuery->filter->brand = true;
                    $productDescriptionQuery->filter->tag = true;
                    $productDescriptionQuery->prepare();

                    if ($config->product['getModelInCard']) {
                        $productModelQuery = new Query\Product\Model\GetByTokenList([$request->productCriteria['token']], $regionQuery->response->region['id']);
                        $productModelQuery->prepare();
                    }
                } else if ($request->productCriteria['barcode']) { // Используется в ветке lite
                    $productQuery = new Query\Product\GetByBarcode($request->productCriteria['barcode'], $regionQuery->response->region['id']);
                    $productQuery->prepare();

                    $productDescriptionQuery = new Query\Product\GetDescriptionByBarcodeList();
                    $productDescriptionQuery->barcodes = [$request->productCriteria['barcode']];
                    $productDescriptionQuery->filter->trustfactor = true;
                    $productDescriptionQuery->filter->category = true;
                    $productDescriptionQuery->filter->media = true;
                    $productDescriptionQuery->filter->seo = true;
                    $productDescriptionQuery->filter->property = true;
                    $productDescriptionQuery->filter->label = true;
                    $productDescriptionQuery->filter->brand = true;
                    $productDescriptionQuery->filter->tag = true;
                    $productDescriptionQuery->prepare();

                    if (true || $config->product['getModel']) {
                        $productModelQuery = new Query\Product\Model\GetByBarcodeList([$request->productCriteria['barcode']], $regionQuery->response->region['id']);
                        $productModelQuery->prepare();
                    }
                } else {
                    throw new \InvalidArgumentException('Неверный критерий получения товара');
                }
            });

            // дерево категорий для меню
            $categoryRootTreeQuery =
                ('on' !== \App::request()->headers->get('SSI'))
                ? (new Query\Product\Category\GetRootTree($regionQuery->response->region['id'], 3))->prepare()
                : null
            ;

            // пользователь и его подписки
            /** @var Query\User\GetByToken $userQuery */
            $userQuery = null;
            if ($request->userToken) {
                // пользователь
                $userQuery = (new Query\User\GetByToken($request->userToken))->prepare();
            }

            // каналы подписок
            $subscribeChannelQuery =
                $config->subscribe['getChannel']
                ? (new Query\Subscribe\Channel\Get())->prepare()
                : null
            ;
            
            // выполнение запросов
            $curl->execute();

            // SITE-5975 Не отображать товары, по которым scms или ядро не вернуло данных
            if (empty($productQuery->response->product['ui']) || empty($productDescriptionQuery->response->products[0]['uid'])) {
                $response = new Response();
                $response->regionQuery = $regionQuery;
                $response->redirectQuery = $redirectQuery;
                $response->abTestQuery = $abTestQuery;
                $response->menuQuery = $menuQuery;
                $response->productQuery = $productQuery;
                $response->productDescriptionQuery = $productDescriptionQuery;
                $response->productModelQuery = $productModelQuery;
                $response->categoryRootTreeQuery = $categoryRootTreeQuery;
                $response->userQuery = $userQuery;
                $response->subscribeChannelQuery = $subscribeChannelQuery;
                return $response;
            }

            // аксессуары
            /** @var Query\Product\GetByIdList[] $accessoryProductQueries */
            $accessoryProductQueries = [];
            /** @var Query\Product\GetByIdList[] $accessoryProductDescriptionQueries */
            $accessoryProductDescriptionQueries = [];
            call_user_func(function() use (&$accessoryProductQueries, &$accessoryProductDescriptionQueries, $productQuery, &$config) {
                if (empty($productQuery->response->product['accessories']) || !is_array($productQuery->response->product['accessories'])) {
                    return;
                }

                $accessoryIds = array_slice($productQuery->response->product['accessories'], 0, $config->product['itemsPerPage']);
                foreach (array_chunk($accessoryIds, $config->coreV2['chunk_size']) as $idsInChunk) {
                    $accessoryProductQuery = new Query\Product\GetByIdList($idsInChunk, $productQuery->regionId);
                    $accessoryProductQuery->prepare();
                    $accessoryProductQueries[] = $accessoryProductQuery;

                    $accessoryProductDescriptionQuery = new Query\Product\GetDescriptionByIdList();
                    $accessoryProductDescriptionQuery->ids = $idsInChunk;
                    $accessoryProductDescriptionQuery->filter->category = true;
                    $accessoryProductDescriptionQuery->filter->media = true;
                    $accessoryProductDescriptionQuery->filter->label = true;
                    $accessoryProductDescriptionQuery->prepare();
                    $accessoryProductDescriptionQueries[] = $accessoryProductDescriptionQuery;
                }
            });

            // наборы
            /** @var Query\Product\GetByIdList[] $kitProductQueries */
            $kitProductQueries = [];
            /** @var Query\Product\GetByIdList[] $kitProductDescriptionQueries */
            $kitProductDescriptionQueries = [];
            call_user_func(function() use (&$kitProductQueries, &$kitProductDescriptionQueries, $productQuery, &$config) {
                if (empty($productQuery->response->product['kit']) || !is_array($productQuery->response->product['kit'])) {
                    return;
                }

                $kitIds = array_column($productQuery->response->product['kit'], 'id');
                foreach (array_chunk($kitIds, $config->coreV2['chunk_size']) as $idsInChunk) {
                    $kitProductQuery = new Query\Product\GetByIdList($idsInChunk, $productQuery->regionId);
                    $kitProductQuery->prepare();
                    $kitProductQueries[] = $kitProductQuery;

                    $kitProductDescriptionQuery = new Query\Product\GetDescriptionByIdList();
                    $kitProductDescriptionQuery->ids = $idsInChunk;
                    $kitProductDescriptionQuery->filter->media = true;
                    $kitProductDescriptionQuery->filter->property = true;
                    if ($config->lite['enabled']) {
                        $kitProductDescriptionQuery->filter->label = true;
                        $kitProductDescriptionQuery->filter->brand = true;
                        $kitProductDescriptionQuery->filter->category = true;
                    }

                    $kitProductDescriptionQuery->prepare();
                    $kitProductDescriptionQueries[] = $kitProductDescriptionQuery;
                }
            });

            /** @var \EnterQuery\Product\Similar\GetUiListByProductUi|null $similarProductUiListQuery */
            $similarProductUiListQuery = null;
            call_user_func(function() use (&$productQuery, &$regionQuery, &$similarProductUiListQuery, &$config) {
                $productUi = $productQuery->response->product['ui'];
                if (!$productUi || !$config->mainMenu['recommendationsEnabled']) {
                    return;
                }

                $similarProductUiListQuery = (new Query\Product\Similar\GetUiListByProductUi($productUi, $regionQuery->response->region['id']))->prepare();
            });

            $curl->execute();

            call_user_func(function() use (&$similarProductQuery, &$similarProductDescriptionQuery, $similarProductUiListQuery, $regionQuery) {
                if (!$similarProductUiListQuery) return;
                $productUis = array_merge($similarProductUiListQuery->response->beforeProductUis, $similarProductUiListQuery->response->afterProductUis);
                if (!$productUis) return;

                $similarProductQuery = new Query\Product\GetByUiList($productUis, $regionQuery->response->region['id']);
                $similarProductQuery->prepare();

                $similarProductDescriptionQuery = new Query\Product\GetDescriptionByUiList();
                $similarProductDescriptionQuery->uis = $productUis;
                $similarProductDescriptionQuery->prepare();
            });

            call_user_func(function() use (&$productQuery, &$userQuery, &$productViewEventQuery, &$config) {
                $productUi = $productQuery->response->product['ui'];
                if (!$productUi || !$userQuery || !$config->eventService['enabled']) return;

                // product view событие
                $productViewEventQuery = (new Query\Event\PushProductView($productUi, $userQuery->response->user['ui']))->prepare();
            });

            call_user_func(function() use (&$productQuery, &$couponQuery, &$config) {
                if (empty($productQuery->response->product['ui']) || !$config->product['couponEnabledInCard']) {
                    return;
                }

                $couponQuery = new Query\Product\Coupon\GetCouponByProductsUi($productQuery->response->product['ui']);
            });

            // доставка
            call_user_func(function() use (&$productQuery, &$deliveryQuery, $kitProductQueries, $kitProductDescriptionQueries, &$config) {
                $productId = $productQuery->response->product['id'];
                if (!$productId || !$config->product['deliveryCalc']) {
                    return;
                }

                $deliveryQuery = new Query\Delivery\GetByCart();
                // корзина
                $deliveryQuery->cart->addProduct($productId, 1);

                $commonKitIds = [];
                // SITE-5975 Не отображать товары, по которым scms или ядро не вернуло данных
                call_user_func(function() use(&$commonKitIds, $kitProductQueries, $kitProductDescriptionQueries) {
                    if (!$kitProductQueries || !$kitProductDescriptionQueries) {
                        return;
                    }

                    $kitProductIds = [];
                    $kitProductDescriptionIds = [];
                    foreach ($kitProductQueries as $kitProductQuery) {
                        $kitProductIds = array_merge($kitProductIds, array_column($kitProductQuery->response->products, 'id'));
                    }

                    foreach ($kitProductDescriptionQueries as $kitProductDescriptionQuery) {
                        $kitProductDescriptionIds = array_merge($kitProductDescriptionIds, array_column($kitProductDescriptionQuery->response->products, 'core_id'));
                    }

                    $commonKitIds = array_intersect($kitProductIds, $kitProductDescriptionIds);
                });

                if (false) { // SITE-6654
                    foreach ($commonKitIds as $kitId) {
                        $deliveryQuery->cart->addProduct($kitId, 1);
                    }
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
            call_user_func(function() use (&$productQuery, &$shopQuery, &$config) {
                $shopIds = [];

                if (!$config->product['deliveryCalc']) {
                    return;
                }

                foreach ($productQuery->response->product['stock'] as $stock) {
                    if (!$stock['shop_id'] || !($stock['quantity'] + $stock['quantity_showroom'])) continue;

                    $shopIds[] = $stock['shop_id'];
                }
                if ($shopIds) {
                    $shopQuery = (new Query\Shop\GetByIdList($shopIds))->prepare();
                }
            });

            // группы оплаты
            call_user_func(function() use (&$productQuery, &$paymentGroupQuery, &$config) {
                $productId = $productQuery->response->product['id'];
                if (!$productId || !$config->product['creditEnabledInCard']) return;

                $paymentGroupQuery = new Query\PaymentGroup\GetByCart();
                // корзина
                $paymentGroupQuery->cart->addProduct($productId, 1);
                // регион
                $paymentGroupQuery->regionId = $productQuery->regionId;
                // фильтер
                $paymentGroupQuery->filter->isCorporative = false;
                $paymentGroupQuery->filter->isCredit = true;
                $paymentGroupQuery->filter->noDiscount = true;

                $paymentGroupQuery->prepare();
            });

            // рейтинг товаров
            call_user_func(function() use (&$productQuery, &$ratingQuery, &$config) {
                if (!$config->product['reviewEnabled']) return;

                $ids = []; // идентификаторы товаров
                if ($accessoryIds = array_slice((array)$productQuery->response->product['accessories'], 0, $config->product['itemsPerPage'])) {
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

            // отзывы товара
            call_user_func(function() use (&$productQuery, &$reviewQuery, &$config) {
                if (!$config->product['reviewEnabled']) return;

                $productUi = $productQuery->response->product['ui'];
                if (!$productUi) return;
                $pageSize = 10;
                $reviewQuery = (new Query\Product\Review\GetByProductUi($productUi, 0, $pageSize, new \Model\Review\Sorting()))->prepare();
            });

            // категория товаров
            call_user_func(function() use (&$categoryQuery, $regionQuery, $productDescriptionQuery, &$config) {
                if (
                    empty($productDescriptionQuery->response->products[0]['categories'])
                    || !is_array($productDescriptionQuery->response->products[0]['categories'])
                    || !$config->product['breadcrumbsEnabled']
                ) {
                    return;
                }

                $categoryUi = null;
                foreach ($productDescriptionQuery->response->products[0]['categories'] as $category) {
                    if ($category['main']) {
                        $categoryUi = $category['uid'];
                        break;
                    }
                }

                if (!$categoryUi) {
                    return;
                }

                $categoryQuery = (new Query\Product\Category\GetByUi($categoryUi, $regionQuery->response->region['id']))->prepare();
            });

            // избранные товары пользователя
            call_user_func(function() use (&$userQuery, &$productQuery, &$favoriteQuery) {
                $userUi = ($userQuery && $userQuery->response->user['ui']) ? $userQuery->response->user['ui'] : null;
                $productUi = $productQuery->response->product['ui'];

                $favoriteQuery = new Query\User\Favorite\Check($userUi, [$productUi]);
                if ($productUi && $userUi) $favoriteQuery->prepare();
            });

            // товар для Подари Жизнь
            /** @var Query\Product\GetByUi $lifeGiftProductQuery|null */
            /** @var Query\Product\GetDescriptionByUiList $lifeGiftProductDescriptionQuery|null */
            call_user_func(function() use (&$productQuery, &$productDescriptionQuery, &$lifeGiftProductQuery, &$lifeGiftProductDescriptionQuery, &$config) {
                $product = $productQuery->response->product;
                if (
                    !empty($product['ui'])
                    && !empty($productDescriptionQuery->response->products[0]['label']['core_id'])
                    && $config->lifeGift['enabled']
                    && $config->lifeGift['labelId'] === (int)$productDescriptionQuery->response->products[0]['label']['core_id']
                ) {
                    $lifeGiftProductQuery = new Query\Product\GetByUi($product['ui'], $config->lifeGift['regionId']);

                    $lifeGiftProductDescriptionQuery = new Query\Product\GetDescriptionByUiList();
                    $lifeGiftProductDescriptionQuery->uis = [$product['ui']];
                    $lifeGiftProductDescriptionQuery->filter->label = true;
                    $lifeGiftProductDescriptionQuery->prepare();
                }
            });

            // выполнение запросов
            $curl->execute();

            $this->removeCurl();

            // обработка ошибок
            if ($menuQuery && $menuQuery->error) {
                $menuQuery->response->items = \App::dataStoreClient()->query('/main-menu.json')['item'];

                \App::logger()->error(['error' => $menuQuery->error, 'sender' => __FILE__ . ' ' .  __LINE__], ['main_menu', 'controller']);
            }

            // response
            $response = new Response();
            $response->productQuery = $productQuery;
            $response->productDescriptionQuery = $productDescriptionQuery;
            $response->productModelQuery = $productModelQuery;
            $response->userQuery = $userQuery;
            $response->favoriteQuery = $favoriteQuery;
//            $response->subscribeQuery = $subscribeQuery;
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
            $response->accessoryProductQueries = $accessoryProductQueries;
            $response->accessoryProductDescriptionQueries = $accessoryProductDescriptionQueries;
            $response->kitProductQueries = $kitProductQueries;
            $response->kitProductDescriptionQueries = $kitProductDescriptionQueries;
            $response->reviewQuery = $reviewQuery;
            $response->categoryQuery = $categoryQuery;
            $response->lifeGiftProductQuery = $lifeGiftProductQuery;
            $response->lifeGiftProductDescriptionQuery = $lifeGiftProductDescriptionQuery;
            $response->similarProductQuery = $similarProductQuery;
            $response->similarProductDescriptionQuery = $similarProductDescriptionQuery;
            $response->couponQuery = $couponQuery;

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
        /** @var Query\Product\GetByToken|Query\Product\GetByBarcode */
        public $productQuery;
        /** @var Query\Product\GetDescriptionByTokenList|Query\Product\GetDescriptionByBarcodeList */
        public $productDescriptionQuery;
        /** @var Query\Product\Model\GetByTokenList|Query\Product\Model\GetByBarcodeList */
        public $productModelQuery;
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
        public $accessoryProductQueries = [];
        /** @var Query\Product\GetByIdList[] */
        public $accessoryProductDescriptionQueries = [];
        /** @var Query\Product\GetByIdList[] */
        public $kitProductQueries = [];
        /** @var Query\Product\GetByIdList[] */
        public $kitProductDescriptionQueries = [];
        /** @var Query\Product\Review\GetByProductUi|null */
        public $reviewQuery;
        /** @var Query\Product\Category\GetByUi|null */
        public $categoryQuery;
        /** @var Query\User\Favorite\Check|null */
        public $favoriteQuery;
        /** @var Query\Product\GetByToken|null */
        public $lifeGiftProductQuery;
        /** @var Query\Product\GetDescriptionByUiList|null */
        public $lifeGiftProductDescriptionQuery;
        /** @var Query\Product\Coupon\GetCouponByProductsUi|null */
        public $couponQuery;
        /** @var Query\Product\GetByUiList|null */
        public $similarProductQuery;
        /** @var Query\Product\GetDescriptionByUiList|null */
        public $similarProductDescriptionQuery;
    }
}