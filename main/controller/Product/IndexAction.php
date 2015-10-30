<?php

namespace Controller\Product;

use Model\ClosedSale\ClosedSaleEntity;
use Model\Product\Trustfactor;

class IndexAction {
    /** @var \EnterApplication\Action\ProductCard\Get\Response|null */
    public static $actionResponse; // осторожно, вынужденный г*код

    /**
     * @param string $productPath
     * @param \Http\Request $request
     * @return \Http\Response
     */
    public function execute($productPath, \Http\Request $request) {
        $actionResponse = self::$actionResponse;

        // регион
        $region =
            $actionResponse->regionQuery->response->region
            ? new \Model\Region\Entity($actionResponse->regionQuery->response->region)
            : null
        ;

        // товар
        /** @var \Model\Product\Entity $product */
        /** @var Trustfactor[] $trustfactors */
        $trustfactors = [];
        call_user_func(function() use (&$actionResponse, &$product, &$trustfactors) {
            if (empty($actionResponse->productQuery->response->product['ui'])) {
                throw new \Exception\NotFoundException('Товар не получен от ядра');
            } else {
                $product = new \Model\Product\Entity($actionResponse->productQuery->response->product);
            }

            if (empty($actionResponse->productDescriptionQuery->response->products[0]['uid'])) {
                // SITE-5975 Не отображать товары, по которым scms или ядро не вернуло данных
                throw new \Exception\NotFoundException('Товар не получен от scms');
            } else {
                $productDescription = $actionResponse->productDescriptionQuery->response->products[0];
                $product->importFromScms($productDescription);

                if (isset($productDescription['trustfactors']) && is_array($productDescription['trustfactors'])) {
                    foreach ($productDescription['trustfactors'] as $trustfactor) {
                        if (is_array($trustfactor)) {
                            $trustfactors[] = new Trustfactor($trustfactor);
                        }
                    }
                }

                // Трастфакторы "Спасибо от Сбербанка" и Много.ру не должны отображаться на партнерских товарах
                if ($product->getPartnersOffer()) {
                    foreach ($trustfactors as $key => $trustfactor) {
                        if ('right' === $trustfactor->type
                            && in_array($trustfactor->uid, [
                                Trustfactor::UID_MNOGO_RU,
                                Trustfactor::UID_SBERBANK_SPASIBO
                            ])) {
                            unset($trustfactors[$key]);
                        }
                    }
                }
            }

            if (!empty($actionResponse->productModelQuery->response->products[0])) {
                $product->importModelFromScms($actionResponse->productModelQuery->response->products[0]);
            }
        });

        // товар для Подари Жизнь
        $lifeGiftProduct = null;
        if (
            !empty($actionResponse->lifeGiftProductQuery->response->product)
            && !empty($actionResponse->lifeGiftProductDescriptionQuery->response->products[0]) // SITE-5975 Не отображать товары, по которым scms или ядро не вернуло данных
        ) {
            $lifeGiftProduct = new \Model\Product\Entity($actionResponse->lifeGiftProductQuery->response->product);
            $lifeGiftProduct->importFromScms($actionResponse->lifeGiftProductDescriptionQuery->response->products[0]);
        }

        $catalogJson =
            ($actionResponse->categoryQuery && $actionResponse->categoryQuery->response->category)
            ? (new \Model\Product\Category\Entity($actionResponse->categoryQuery->response->category))->catalogJson
            : []
        ;

        $reviewsData =
            $actionResponse->reviewQuery
            ? [
                'review_list'            => $actionResponse->reviewQuery->response->reviews,
                'num_reviews'            => $actionResponse->reviewQuery->response->reviewCount,
                'avg_score'              => $actionResponse->reviewQuery->response->score,
                'avg_star_score'         => $actionResponse->reviewQuery->response->starScore,
                'current_page_avg_score' => $actionResponse->reviewQuery->response->currentPageAvgScore,
                'num_users_by_score'     => $actionResponse->reviewQuery->response->groupedScoreCount,
                'page_count'             => $actionResponse->reviewQuery->response->pageCount,
            ]
            : []
        ;

        // получаем рейтинги
        $reviewsDataSummary = [];
        if (\App::config()->product['reviewEnabled']) {
            if(!empty($reviewsData['avg_score'])) $product->setAvgScore($reviewsData['avg_score']);
            if(!empty($reviewsData['avg_star_score'])) $product->setAvgStarScore($reviewsData['avg_star_score']);
            if(!empty($reviewsData['num_reviews'])) $product->setNumReviews($reviewsData['num_reviews']);

            $reviewsDataSummary = \RepositoryManager::review()->getReviewsDataSummary($reviewsData);
        }

        // аксессуары
        /** @var \Model\Product\Entity[] $accessories */
        $accessories = [];
        call_user_func(function() use(&$accessories, &$product, $actionResponse) {
            // SITE-5975 Не отображать товары, по которым scms или ядро не вернуло данных
            call_user_func(function() use(&$accessories, $actionResponse) {
                if ($actionResponse->accessoryProductQueries && $actionResponse->accessoryProductDescriptionQueries) {
                    $accessoryProductDescriptionQueryByUi = [];
                    foreach ($actionResponse->accessoryProductDescriptionQueries as $accessoryProductDescriptionQuery) {
                        foreach ($accessoryProductDescriptionQuery->response->products as $product) {
                            $accessoryProductDescriptionQueryByUi[$product['uid']] = $product;
                        }
                    }

                    foreach ($actionResponse->accessoryProductQueries as $accessoryProductQuery) {
                        foreach ($accessoryProductQuery->response->products as $product) {
                            if (isset($accessoryProductDescriptionQueryByUi[$product['ui']])) {
                                $accessoryProduct = new \Model\Product\Entity($product);
                                $accessoryProduct->importFromScms($accessoryProductDescriptionQueryByUi[$product['ui']]);
                                $accessories[$accessoryProduct->id] = $accessoryProduct;
                            }
                        }
                    }
                }
            });

            $product->setAccessoryId(array_map(function(\Model\Product\Entity $product) { return $product->id; }, $accessories));
        });

        $accessoryCategories = [];
        call_user_func(function() use(&$accessoryCategories, $accessories, $catalogJson) {
            $jsonCategoryToken = isset($catalogJson['accessory_category_token']) ? $catalogJson['accessory_category_token'] : null;

            if (empty($jsonCategoryToken)) {
                return [];
            }

            // отсеиваем среди текущих аксессуаров те аксессуары, которые не относятся к разрешенным категориям
            $accessories = array_filter($accessories, function(\Model\Product\Entity $accessory) use(&$jsonCategoryToken) {
                // есть ли общие категории между категориями аксессуара и разрешенными в json
                return array_intersect($jsonCategoryToken, array_map(function(\Model\Product\Category\Entity $accessoryCategory) {
                    return $accessoryCategory->getToken();
                }, $accessory->getCategory()));
            });

            foreach ($accessories as $product) {
                /** @var \Model\Product\Entity $product */
                $lastCategory = $product->getParentCategory();
                if ($lastCategory && !isset($accessoryCategories[$lastCategory->getToken()])) {
                    $accessoryCategories[$lastCategory->getToken()] = $lastCategory;
                }
            }

            $accessoryCategories = array_values($accessoryCategories);

            if ($accessoryCategories) {
                $popularAccessoryCategory = new \Model\Product\Category\Entity();
                $popularAccessoryCategory->setId(0);
                $popularAccessoryCategory->setName('Популярные аксессуары');
                array_unshift($accessoryCategories, $popularAccessoryCategory);
            }
        });

        // SITE-5035
        $similarProducts = [];
        call_user_func(function() use($actionResponse, &$similarProducts) {
            // SITE-5975 Не отображать товары, по которым scms или ядро не вернуло данных
            if (empty($actionResponse->similarProductQuery->response->products) || empty($actionResponse->similarProductDescriptionQuery->response->products[0])) {
                return;
            }

            $similarProductDescriptionUis = array_column($actionResponse->similarProductDescriptionQuery->response->products, 'uid');

            foreach ($actionResponse->similarProductQuery->response->products as $product) {
                if (in_array($product['ui'], $similarProductDescriptionUis, true)) {
                    $similarProducts[] = new \Model\Product\Entity($product);
                }
            }
        });

        // наборы
        /** @var \Model\Product\Entity[] $kit */
        $kit = [];
        /** @var array */
        $kitProducts = [];
        call_user_func(function() use(&$kit, &$kitProducts, &$product, $actionResponse) {
            // SITE-5975 Не отображать товары, по которым scms или ядро не вернуло данных
            call_user_func(function() use(&$kit, $actionResponse) {
                if ($actionResponse->kitProductQueries && $actionResponse->kitProductDescriptionQueries) {
                    $kitProductDescriptionQueryByUi = [];
                    foreach ($actionResponse->kitProductDescriptionQueries as $kitProductDescriptionQuery) {
                        foreach ($kitProductDescriptionQuery->response->products as $product) {
                            $kitProductDescriptionQueryByUi[$product['uid']] = $product;
                        }
                    }

                    foreach ($actionResponse->kitProductQueries as $kitProductQuery) {
                        foreach ($kitProductQuery->response->products as $product) {
                            if (isset($kitProductDescriptionQueryByUi[$product['ui']])) {
                                $kitProduct = new \Model\Product\Entity($product);
                                $kitProduct->importFromScms($kitProductDescriptionQueryByUi[$product['ui']]);
                                $kit[] = $kitProduct;
                            }
                        }
                    }
                }
            });

            $kitProducts = \RepositoryManager::product()->getKitProducts($product, $kit, $actionResponse->deliveryQuery);
        });

        // если в catalogJson'e указан category_class, то обрабатываем запрос соответствующим контроллером
        $categoryClass = !empty($catalogJson['category_class']) ? $catalogJson['category_class'] : null;
        // карточку показываем в обычном лэйауте, если включена соответствующая настройка
        if(!empty($catalogJson['regular_product_page'])) $categoryClass = null;

        $actionChannelName = '';
        if ($actionResponse->subscribeChannelQuery) {
            foreach ($actionResponse->subscribeChannelQuery->response->channels as $item) {
                $channel = new \Model\Subscribe\Channel\Entity($item);
                if (1 == $channel->getId()) {
                    $actionChannelName = $channel->getName();
                    break;
                }
            }
        }

        // кредит
        $creditData = $this->getDataForCredit($product, $actionResponse->paymentGroupQuery);

        // наличие в магазинах
        /** @var $shopStates \Model\Product\ShopState\Entity[] */
        $shopStates = [];
        $quantityByShop = [];
        foreach ($product->getStock() as $stock) {
            $quantityShowroom = (int)$stock->getQuantityShowroom();
            $quantity = (int)$stock->getQuantity();
            $shopId = $stock->getShopId();
            if ((0 < $quantity + $quantityShowroom) && !empty($shopId)) {
                $quantityByShop[$shopId] = [
                    'quantity' => $quantity,
                    'quantityShowroom' => $quantityShowroom,
                ];
            }
        }
        if ($quantityByShop && $actionResponse->shopQuery) {
            foreach ($actionResponse->shopQuery->response->shops as $item) {
                $shop = new \Model\Shop\Entity($item);

                if ($shop->getWorkingTimeToday()) {

                    $shopState = new \Model\Product\ShopState\Entity();

                    $shopState->setShop($shop);
                    $shopState->setQuantity(isset($quantityByShop[$shop->getId()]['quantity']) ? $quantityByShop[$shop->getId()]['quantity'] : 0);
                    $shopState->setQuantityInShowroom(isset($quantityByShop[$shop->getId()]['quantityShowroom']) ? $quantityByShop[$shop->getId()]['quantityShowroom'] : 0);

                    $shopStates[] = $shopState;

                }
            }
        }

        // на товар перешли с блока рекомендаций
        $addToCartJS = null;
        if ('cart_rec' === $request->get('from')) {
            // пишем в сессию id товара
            if ($sessionName = \App::config()->product['recommendationSessionKey']) {
                $storage = \App::session();
                $limit = \App::config()->product['recommendationProductLimit'];

                $data = (array)$storage->get($sessionName, []);
                if (!in_array($product->getId(), $data)) {
                    $data[] = $product->getId();
                }

                $productCount = count($data);
                if ($productCount > $limit) {
                    $data = array_slice($data, $productCount - $limit, $limit, true);
                }

                $storage->set($sessionName, $data);
            }

            // Retailrocket. Добавление товара в корзину
            if ($request->get('rrMethod')) {
                $addToCartJS = "try{rrApi.recomAddToCart({$product->getId()}, {methodName: '{$request->get('rrMethod')}'})}catch(e){}";
            }
        }

        if ($product->getSlotPartnerOffer()) {
            $page = new \View\Product\SlotPage();
        } else if ($product->isGifteryCertificate()) {
            $page = new \View\Product\GifteryPage();
        } else {
            $page = new \View\Product\IndexPage();
        }

        $this->setClosedSale($request, $page);

        (new \Controller\Product\DeliveryAction())->getResponseData([['id' => $product->getId()]], $region->getId(), $actionResponse->deliveryQuery, $product);

        // избранные товары
        $favoriteProductsByUi = [];
        foreach ($actionResponse->favoriteQuery->response->products as $item) {
            if (!isset($item['is_favorite']) || !$item['is_favorite']) continue;

            $ui = isset($item['uid']) ? (string)$item['uid'] : null;
            if (!$ui) continue;

            $favoriteProductsByUi[$ui] = new \Model\Favorite\Product\Entity($item);
        }

        if ($actionResponse->couponQuery) {
            $product->setCoupons($actionResponse->couponQuery->response->getCouponsForProduct($product->getUi()));
        }

        $page->setParam('renderer', \App::closureTemplating());
        $page->setParam('product', $product);
        $page->setParam('lifeGiftProduct', $lifeGiftProduct);
        $page->setParam('title', $product->getName());
        $page->setParam('accessories', $accessories);
        $page->setParam('accessoryCategory', $accessoryCategories);
        $page->setParam('kit', $kit);
        $page->setParam('kitProducts', $kitProducts);
        $page->setParam('creditData', $creditData);
        $page->setParam('shopStates', $shopStates);
        $page->setParam('reviewsData', $reviewsData);
        $page->setParam('reviewsDataSummary', $reviewsDataSummary);
        $page->setParam('categoryClass', $categoryClass);
        $page->setParam('catalogJson', $catalogJson);
        $page->setParam('trustfactors', $trustfactors);
        $page->setParam('favoriteProductsByUi', $favoriteProductsByUi);
        $page->setParam('deliveryData', (new \Controller\Product\DeliveryAction())->getResponseData([['id' => $product->getId()]], $region->getId(), $actionResponse->deliveryQuery, $product));
//        $page->setParam('isUserSubscribedToEmailActions', $isUserSubscribedToEmailActions);
        $page->setParam('actionChannelName', $actionChannelName);
        $page->setGlobalParam('from', $request->get('from') ? $request->get('from') : null);
        $page->setParam('viewParams', [
            'showSideBanner' => \Controller\ProductCategory\Action::checkAdFoxBground($catalogJson)
        ]);
        $page->setGlobalParam('isTchibo', ($product->getRootCategory() && 'Tchibo' === $product->getRootCategory()->getName()));
        $page->setGlobalParam('addToCartJS', $addToCartJS);
        $page->setGlobalParam('similarProducts', $similarProducts);

        return new \Http\Response($page->show());
    }

    /**
     * Устанавливает параметр для View
     *
     * @param \Http\Request $request
     * @param \View\DefaultLayout $page
     */
    public function setClosedSale(\Http\Request $request, \View\DefaultLayout $page) {

        if (!$uid = $request->query->get('secretsaleUid')) {
            return;
        }

        $closedSaleResponse = \App::scmsClient()->query('api/promo-sale/get', ['uid' => [$uid]]);

        if (array_key_exists(0, $closedSaleResponse)) {
            $page->setParam('closedSale', new ClosedSaleEntity($closedSaleResponse[0]));
        }

    }

    /**
     * Собирает в массив данные, необходимые для плагина online кредитовария // скопировано из symfony
     *
     * @param $product
     * @return array
     */
    public function getDataForCredit(\Model\Product\Entity $product, \EnterQuery\PaymentGroup\GetByCart $paymentGroupQuery = null) {

        if (!\App::config()->payment['creditEnabled']) return ['creditIsAllowed' => false];

        $user = \App::user();
        $region = $user->getRegion();

        $result = [];

        $category = $product->getRootCategory();
        $cart = \App::user()->getCart();
        try {
            $productType = $category ? \RepositoryManager::creditBank()->getCreditTypeByCategoryToken($category->getToken()) : '';
        } catch (\Exception $e) {
            \App::exception()->add($e);
            \App::logger()->error($e);

            $productType = '';
        }

        // SITE-4076 Учитывать возможность кредита из API
        $hasCreditPaymentMethod = false;
        $successCallback = function($data) use (&$hasCreditPaymentMethod) {
            if (!isset($data['detail']) || !is_array($data['detail'])) {
                return;
            }

            foreach ($data['detail'] as $group) {
                $paymentGroup = new \Model\PaymentMethod\Group\Entity($group);
                if (!$paymentGroup->getPaymentMethods()) {
                    continue;
                }

                // выкидываем заблокированные методы
                $blockedIds = (array)\App::config()->payment['blockedIds'];
                $filteredMethods = array_filter($paymentGroup->getPaymentMethods(), function(\Model\PaymentMethod\Entity $method) use ($blockedIds) {
                    if (in_array($method->getId(), $blockedIds)) return false;
                    return true;
                });
                $paymentGroup->setPaymentMethods($filteredMethods);

                if (empty($filteredMethods)) {
                    continue;
                }

                // пробегаем по методах и ищем метод "Покупка в кредит"
                foreach ($filteredMethods as $method) {
                    if (
                        !$method instanceof \Model\PaymentMethod\Entity ||
                        $method->getId() != \Model\PaymentMethod\Entity::CREDIT_ID
                    ) {
                        continue;
                    }

                    $hasCreditPaymentMethod = true;
                }
            }
        };

        if ($paymentGroupQuery) {
            call_user_func($successCallback, ['detail' => $paymentGroupQuery->response->paymentGroups]);
        } else {
            \RepositoryManager::paymentGroup()->prepareCollection($region,
                [
                    'is_corporative' => false,
                    'is_credit'      => true,
                ],
                [
                    'product_list'   => [['id' => $product->getId(), 'quantity' => (($cart->getProductQuantity($product->getId()) > 0) ? $cart->getProductQuantity($product->getId()) : 1)]],
                ],
                $successCallback,
                function($e){
                    \App::exception()->remove($e);
                    \App::logger()->error($e);
                }
            );
            \App::curl()->execute();
        }

        $dataForCredit = array(
            'price'        => $product->getPrice(),
            //'articul'      => $product->getArticle(),
            'name'         => $product->getName(),
            'count'        => 1, //$cart->getProductQuantity($product->getId()),
            'product_type' => $productType,
            'session_id'   => session_id()
        );

        $result['creditIsAllowed'] = $hasCreditPaymentMethod;
        $result['creditData'] = json_encode($dataForCredit);

        return $result;
    }
}