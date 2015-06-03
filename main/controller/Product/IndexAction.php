<?php

namespace Controller\Product;

use Model\Product\Label;
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

        $productDescriptionsByUi = [];
        call_user_func(function() use (&$actionResponse, &$productDescriptionsByUi) {
            foreach ($actionResponse->productDescriptionQueries as $productDescriptionQuery) {
                if (is_array($productDescriptionQuery->response->products)) {
                    foreach ($productDescriptionQuery->response->products as $product) {
                        $productDescriptionsByUi[$product['uid']] = $product;
                    }
                }
            }
        });

        // товар
        /** @var $product \Model\Product\Entity */
        call_user_func(function() use (&$actionResponse, &$productDescriptionsByUi, &$product) {
            $productItem = [];
            if ($actionResponse->productQuery->response->product) {
                $productItem = $actionResponse->productQuery->response->product;
            }

            // осторожно! Если ядро не вернуло товар...
            if (empty($productItem['id'])) {
                throw new \Exception\NotFoundException(sprintf(sprintf('Товар @%s не получен от ядра', $productItem['token'])));
            }

            if (isset($productDescriptionsByUi[$productItem['ui']])) {
                $productDescriptionItem = $productDescriptionsByUi[$productItem['ui']];
                // проверка на корректность данных от scms
                if (empty($productDescriptionItem['uid'])) {
                    return;
                }

                $propertyData = isset($productDescriptionItem['properties'][0]) ? $productDescriptionItem['properties'] : [];
                if ($propertyData) {
                    $productItem['property'] = $propertyData;
                }

                $propertyGroupData = isset($productDescriptionItem['property_groups'][0]) ? $productDescriptionItem['property_groups'] : [];
                if ($propertyGroupData) {
                    $productItem['property_group'] = $propertyGroupData;
                }
            }

            $product = $productItem ? new \Model\Product\Entity($productItem) : null;
        });

        if (!$product) {
            throw new \Exception\NotFoundException(sprintf('Товар не найден'));
        }

        /** @var Trustfactor[] $trustfactors */
        $trustfactors = [];
        call_user_func(function() use ($actionResponse, $product, &$productDescriptionsByUi, &$trustfactors) {
            if (!$actionResponse->productDescriptionQueries) return;

            $data = isset($productDescriptionsByUi[$product->getUi()]) ? $productDescriptionsByUi[$product->getUi()] : [];
            if (!$data) return;

            if (isset($data['trustfactors']) && is_array($data['trustfactors'])) {
                foreach ($data['trustfactors'] as $trustfactor) {
                    if (is_array($trustfactor)) {
                        $trustfactors[] = new Trustfactor($trustfactor);
                    }
                }
            }

            // Трастфакторы "Спасибо от Сбербанка" и Много.ру не должны отображаться на партнерских товарах
            if (is_array($product->getPartnersOffer()) && count($product->getPartnersOffer()) != 0) {
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

            if (isset($data['title'])) {
                $product->setSeoTitle($data['title']);
            }

            if (isset($data['meta_keywords'])) {
                $product->setSeoKeywords($data['meta_keywords']);
            }

            if (isset($data['meta_description'])) {
                $product->setSeoDescription($data['meta_description']);
            }

            if (isset($data['medias']) && is_array($data['medias'])) {
                $product->medias = array_map(function($media) { return new \Model\Media($media); }, $data['medias']);
            }

            if (isset($data['label']['uid'])) $product->setLabel(new Label($data['label']));

            if (isset($data['json3d']) && is_array($data['json3d'])) {
                $product->json3d = $data['json3d'];
            }
        });

        // товар для Подари Жизнь
        $lifeGiftProduct =
            ($actionResponse->lifeGiftProductQuery && $actionResponse->lifeGiftProductQuery->response->product)
            ? new \Model\Product\Entity($actionResponse->lifeGiftProductQuery->response->product)
            : null
        ;

        if ($lifeGiftProduct && isset($productDescriptionsByUi[$lifeGiftProduct->getUi()]['medias']) && is_array($productDescriptionsByUi[$lifeGiftProduct->getUi()]['medias'])) {
            $lifeGiftProduct->medias = array_map(function($media) { return new \Model\Media($media); }, $productDescriptionsByUi[$lifeGiftProduct->getUi()]['medias']);
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
            $reviewsDataSummary = \RepositoryManager::review()->getReviewsDataSummary($reviewsData);
        }

        // связанные товары: аксессуары, состав набора, ...
        /** @var \Model\Product\Entity[] $relatedProductsById */
        $relatedProductsById = [];
        foreach ($actionResponse->relatedProductQueries as $relatedProductQuery) {
            foreach ($relatedProductQuery->response->products as $item) {
                $relatedProduct = new \Model\Product\Entity($item);
                if (isset($productDescriptionsByUi[$relatedProduct->getUi()]['medias']) && is_array($productDescriptionsByUi[$relatedProduct->getUi()]['medias'])) {
                    $relatedProduct->medias = array_map(function($media) { return new \Model\Media($media); }, $productDescriptionsByUi[$relatedProduct->getUi()]['medias']);
                }

                $relatedProductsById[$relatedProduct->getId()] = $relatedProduct;
            }
        }

        // аксессуары
        $accessories = [];
        foreach ($product->getAccessoryId() as $accessoryId) {
            $relatedProduct = isset($relatedProductsById[$accessoryId]) ? $relatedProductsById[$accessoryId] : null;
            if (!$relatedProduct) continue;

            $accessories[$relatedProduct->getId()] = $relatedProduct;
        }

        $accessoryItems = [];
        $accessoryCategory = array_map(function($accessoryGrouped){
            return $accessoryGrouped['category'];
        }, \Model\Product\Repository::filterAccessoryId($product, $accessoryItems, null, \App::config()->product['itemsInAccessorySlider'] * 36, $catalogJson, $accessories));
        if ((bool)$accessoryCategory) {
            $firstAccessoryCategory = new \Model\Product\Category\Entity();
            $firstAccessoryCategory->setId(0);
            $firstAccessoryCategory->setName('Популярные аксессуары');
            array_unshift($accessoryCategory, $firstAccessoryCategory);
        }

        // SITE-5035
        // похожие товары
        $similarProducts = [];

        // набор пакеты
        $kit = [];
        $kitProducts = [];
        if ((bool)$product->getKit()) {
            // получим основные товары набора
            $kit = [];
            foreach ($product->getKit() as $part) {
                $part = isset($relatedProductsById[$part->getId()]) ? $relatedProductsById[$part->getId()] : null;
                if ($part) {
                    $kit[] = $part;
                }
            }

            $kitProducts = \RepositoryManager::product()->getKitProducts($product, $kit, $actionResponse->deliveryQuery);
        }

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
                $limit = \App::config()->cart['productLimit'];

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

        $deliveryData = (new \Controller\Product\DeliveryAction())->getResponseData([['id' => $product->getId()]], $region->getId(), $actionResponse->deliveryQuery, $product);

        // избранные товары
        $favoriteProductsByUi = [];
        foreach ($actionResponse->favoriteQuery->response->products as $item) {
            if (!isset($item['is_favorite']) || !$item['is_favorite']) continue;

            $ui = isset($item['uid']) ? (string)$item['uid'] : null;
            if (!$ui) continue;

            $favoriteProductsByUi[$ui] = new \Model\Favorite\Product\Entity($item);
        }

        $page->setParam('coupons', $actionResponse->couponQuery->response->getCouponsForProduct($product->getUi()));
        $page->setParam('renderer', \App::closureTemplating());
        $page->setParam('product', $product);
        $page->setParam('lifeGiftProduct', $lifeGiftProduct);
        $page->setParam('title', $product->getName());
        $page->setParam('accessories', $accessories);
        $page->setParam('accessoryCategory', $accessoryCategory);
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
        $page->setParam('deliveryData', (new \Controller\Product\DeliveryAction())->getResponseData([['id' => $product->getId()]], $region->getId(), $actionResponse->deliveryQuery));
//        $page->setParam('isUserSubscribedToEmailActions', $isUserSubscribedToEmailActions);
        $page->setParam('actionChannelName', $actionChannelName);
        $page->setGlobalParam('from', $request->get('from') ? $request->get('from') : null);
        $page->setParam('viewParams', [
            'showSideBanner' => \Controller\ProductCategory\Action::checkAdFoxBground($catalogJson)
        ]);
        $page->setGlobalParam('isTchibo', ($product->getMainCategory() && 'Tchibo' === $product->getMainCategory()->getName()));
        $page->setGlobalParam('addToCartJS', $addToCartJS);
        $page->setGlobalParam('similarProducts', $similarProducts);

        return new \Http\Response($page->show());
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

        $category = $product->getMainCategory();
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
        $is_credit = (bool)(($product->getPrice() * (($cart->getQuantityByProduct($product->getId()) > 0) ? $cart->getQuantityByProduct($product->getId()) : 1)) >= \App::config()->product['minCreditPrice']);

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
                    'is_credit'      => $is_credit,
                ],
                [
                    'product_list'   => [['id' => $product->getId(), 'quantity' => (($cart->getQuantityByProduct($product->getId()) > 0) ? $cart->getQuantityByProduct($product->getId()) : 1)]],
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
            'count'        => 1, //$cart->getQuantityByProduct($product->getId()),
            'product_type' => $productType,
            'session_id'   => session_id()
        );

        $result['creditIsAllowed'] = $hasCreditPaymentMethod;
//        $result['creditIsAllowed'] = (bool)(($product->getPrice() * (($cart->getQuantityByProduct($product->getId()) > 0) ? $cart->getQuantityByProduct($product->getId()) : 1)) >= \App::config()->product['minCreditPrice']);
        $result['creditData'] = json_encode($dataForCredit);

        return $result;
    }
}