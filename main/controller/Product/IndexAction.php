<?php

namespace Controller\Product;

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

        if (!$actionResponse) {
            return (new \Controller\Product\OldIndexAction())->execute($productPath, $request);
        }

        // регион
        $region =
            $actionResponse->regionQuery->response->region
            ? new \Model\Region\Entity($actionResponse->regionQuery->response->region)
            : null
        ;

        // товар
        /** @var $product \Model\Product\Entity */
        $product =
            $actionResponse->productQuery->response->product
            ? new \Model\Product\Entity($actionResponse->productQuery->response->product)
            : null
        ;
        if (!$product) {
            throw new \Exception\NotFoundException(sprintf('Товар не найден'));
        }

        \Session\ProductPageSenders::add($product->getUi(), $request->query->get('sender'));
        \Session\ProductPageSendersForMarketplace::add($product->getUi(), $request->query->get('sender2'));

        // товар для Подари Жизнь
        $lifeGiftProduct =
            ($actionResponse->lifeGiftProductQuery && $actionResponse->lifeGiftProductQuery->response->product)
            ? new \Model\Product\Entity($actionResponse->lifeGiftProductQuery->response->product)
            : null
        ;

        $catalogJson =
            ($actionResponse->categoryQuery && $actionResponse->categoryQuery->response->category)
            ? (new \Model\Product\Category\Entity($actionResponse->categoryQuery->response->category))->catalogJson
            : []
        ;

        $reviewsData =
            $actionResponse->reviewQuery
            ? [
                'review_list'        => $actionResponse->reviewQuery->response->reviews,
                'num_reviews'        => $actionResponse->reviewQuery->response->reviewCount,
                'avg_score'          => $actionResponse->reviewQuery->response->score,
                'avg_star_score'     => $actionResponse->reviewQuery->response->starScore,
                'num_users_by_score' => $actionResponse->reviewQuery->response->groupedScoreCount,
                'page_count'         => $actionResponse->reviewQuery->response->pageCount,
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

        $line = null;

        // набор пакеты
        $kit = [];
        $relatedKits = [];
        $kitProducts = [];
        if ((bool)$product->getKit()) {
            // получим основные товары набора
            $kit = [];
            foreach ($product->getKit() as $part) {
                $part = isset($relatedProductsById[$part->getId()]) ? $relatedProductsById[$part->getId()] : null;
                if (!$part) continue;

                $kit[] = $part;
            }

            $kitProducts = \RepositoryManager::product()->getKitProducts($product, $kit, $actionResponse->deliveryQuery);
        }

        // если в catalogJson'e указан category_class, то обрабатываем запрос соответствующим контроллером
        $categoryClass = !empty($catalogJson['category_class']) ? $catalogJson['category_class'] : null;
        // карточку показываем в обычном лэйауте, если включена соответствующая настройка
        if(!empty($catalogJson['regular_product_page'])) $categoryClass = null;

        $useLens = false;
        if ( isset($catalogJson['use_lens']) ) {
            if ( $catalogJson['use_lens'] ) $useLens = true;
        }
        else {
            $photos = $product->getPhoto();
            if ( isset($photos[0]) ) {
                if ( $photos[0]->getHeight() > 750 || $photos[0]->getWidth() > 750 ) {
                    $useLens = true;
                }
            }
        }

        // подписка
        $isUserSubscribedToEmailActions = false;
        if ($actionResponse->subscribeQuery) {
            foreach ($actionResponse->subscribeQuery->response->subscribes as $item) {
                $entity = new \Model\Subscribe\Entity($item);
                if (1 == $entity->getChannelId() && 'email' === $entity->getType() && $entity->getIsConfirmed()) {
                    $isUserSubscribedToEmailActions = true;
                    break;
                }
            }
        }

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

        // трастфакторы
        /** @var Trustfactor[] $trustfactors */
        $trustfactors = [];
        call_user_func(function() use ($actionResponse, $product, &$trustfactors) {
            if (!$actionResponse->productDescriptionQuery) return;

            $data =
                isset($actionResponse->productDescriptionQuery->response->products[$product->getUi()])
                ? $actionResponse->productDescriptionQuery->response->products[$product->getUi()]
                : []
            ;
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
                            '10259a2e-ce37-49a7-8971-8366de3337d3', // много.ру
                            'ab3ca73c-6cc4-4820-b303-8165317420d5'  // сбербанк
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
                foreach ($data['medias'] as $media) {
                    if (is_array($media)) {
                        $product->medias[] = new \Model\Media($media);
                    }
                }
            }

            if (isset($data['json3d']) && is_array($data['json3d'])) {
                $product->json3d = $data['json3d'];
            }
        });

        // какая-то хрень
        $additionalData = [];
        $accessoriesCount = 1;
        foreach ($relatedProductsById as $item) {
            if (isset($accessories[$item->getId()])) {
                $accessoriesCount++;
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

        $page->setParam('renderer', \App::closureTemplating());
        $page->setParam('product', $product);
        $page->setParam('lifeGiftProduct', $lifeGiftProduct);
        $page->setParam('title', $product->getName());
        $page->setParam('accessories', $accessories);
        $page->setParam('accessoryCategory', $accessoryCategory);
        $page->setParam('kit', $kit);
        $page->setParam('kitProducts', $kitProducts);
        $page->setParam('relatedKits', $relatedKits);
        $page->setParam('additionalData', $additionalData);
        $page->setParam('creditData', $creditData);
        $page->setParam('shopStates', $shopStates);
        $page->setParam('reviewsData', $reviewsData);
        $page->setParam('reviewsDataSummary', $reviewsDataSummary);
        $page->setParam('categoryClass', $categoryClass);
        $page->setParam('useLens', $useLens);
        $page->setParam('catalogJson', $catalogJson);
        $page->setParam('trustfactors', $trustfactors);
        $page->setParam('line', $line);
        $page->setParam('deliveryData', (new \Controller\Product\DeliveryAction())->getResponseData([['id' => $product->getId()]], $region->getId(), $actionResponse->deliveryQuery));
        $page->setParam('isUserSubscribedToEmailActions', $isUserSubscribedToEmailActions);
        $page->setParam('actionChannelName', $actionChannelName);
        $page->setGlobalParam('from', $request->get('from') ? $request->get('from') : null);
        $page->setParam('viewParams', [
            'showSideBanner' => \Controller\ProductCategory\Action::checkAdFoxBground($catalogJson)
        ]);
        $page->setGlobalParam('isTchibo', ($product->getMainCategory() && 'Tchibo' === $product->getMainCategory()->getName()));
        $page->setGlobalParam('addToCartJS', $addToCartJS);
        $page->setGlobalParam('similarProducts', $similarProducts);

        $page->setParam('sprosikupiReviews', null);
        $page->setParam('shoppilotReviews', null);
        switch (\App::abTest()->getTest('reviews')->getChosenCase()->getKey()) {
            case 'sprosikupi':
                $client = new \SprosiKupi\Client(\App::config()->partners['SprosiKupi'], \App::logger());
                $page->setParam('sprosikupiReviews', $client->query($product->getId()));
                break;
            case 'shoppilot':
                $client = new \ShopPilot\Client(\App::config()->partners['ShopPilot'], \App::logger());
                $page->setParam('shoppilotReviews', $client->query($product->getId()));
                break;
        }

        return new \Http\Response($page->show());
    }

    /**
     * Собирает в массив данные, необходимые для плагина online кредитовария // скопировано из symfony
     *
     * @param $product
     * @return array
     */
    public function getDataForCredit(\Model\Product\Entity $product, \EnterQuery\PaymentGroup\GetByCart $paymentGroupQuery = null) {
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