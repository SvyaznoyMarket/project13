<?php

namespace Controller\Product;

use Model\Product\Trustfactor;

class OldIndexAction {
    /**
     * @deprecated
     * @param string        $productPath
     * @param \Http\Request $request
     * @return \Http\RedirectResponse|\Http\Response
     * @throws \Exception\NotFoundException
     */
    public function execute($productPath, \Http\Request $request) {
        //\App::logger()->debug('Exec ' . __METHOD__);

        $client = \App::coreClientV2();
        $user = \App::user();
        $repository = \RepositoryManager::product();

        $productToken = explode('/', $productPath);
        $productToken = end($productToken);

        // подготовка 1-го пакета запросов

        $regionConfig = [];
        // запрашиваем текущий регион, если есть кука региона
        if ($user->getRegionId()) {
            $regionConfig = (array)\App::dataStoreClient()->query("/region/{$user->getRegionId()}.json");

            \RepositoryManager::region()->prepareEntityById($user->getRegionId(), function($data) {
                $data = reset($data);
                if ((bool)$data) {
                    \App::user()->setRegion(new \Model\Region\Entity($data));
                }
            });
        }

        // выполнение 1-го пакета запросов
        $client->execute(\App::config()->coreV2['retryTimeout']['tiny']);

        $regionEntity = $user->getRegion();
        if ($regionEntity instanceof \Model\Region\Entity) {
            if (array_key_exists('reserve_as_buy', $regionConfig)) {
                $regionEntity->setForceDefaultBuy(false == $regionConfig['reserve_as_buy']);
            }
            $user->setRegion($regionEntity);
        }

        $region = $user->getRegion();
        $lifeGiftRegion = new \Model\Region\Entity(['id' => \App::config()->lifeGift['regionId']]);

        // подготовка 2-го пакета запросов

        // TODO: запрашиваем меню

        // запрашиваем товар по токену
        /** @var $product \Model\Product\Entity */
        $product = null;
        $repository->prepareEntityByToken($productToken, $region, function($data) use (&$product) {
            if (!is_array($data)) return;

            if ($data = reset($data)) {
                $product = new \Model\Product\Entity($data);
            }
        });

        // выполнение 2-го пакета запросов
        $client->execute(\App::config()->coreV2['retryTimeout']['tiny']);

        if (!$product) {
            throw new \Exception\NotFoundException(sprintf('Товар @%s не найден.', $productToken));
        }

        if ($request->getPathInfo() !== $product->getLink()) {
            return new \Http\RedirectResponse($product->getLink() . ((bool)$request->getQueryString() ? ('?' . $request->getQueryString()) : ''), 302);
        }

        \Session\ProductPageSenders::add($product->getUi(), $request->query->get('sender'));
        \Session\ProductPageSendersForMarketplace::add($product->getUi(), $request->query->get('sender2'));

        // подготовка 3-го пакета запросов
        $lifeGiftProduct = null;
        if ($product->getLabel() && (\App::config()->lifeGift['labelId'] === $product->getLabel()->getId())) {
            /** @var $lifeGiftProduct \Model\Product\Entity|null */
            $repository->prepareEntityByToken($productToken, $lifeGiftRegion, function($data) use (&$lifeGiftProduct) {
                $data = reset($data);

                if ((bool)$data) {
                    $lifeGiftProduct = new \Model\Product\Entity($data);
                }
            });
        }

        // получаем catalog json
        $catalogJson = [];
        if ($product->getLastCategory() && $product->getLastCategory()->getUi()) {
            \App::scmsClient()->addQuery('category/get/v1', ['uid' => $product->getLastCategory()->getUi(), 'geo_id' => $user->getRegion()->getId()], [], function ($data) use (&$catalogJson) {
                if ($data) {
                    $catalogJson = (new \Model\Product\Category\Entity($data))->catalogJson;
                }
            });
        }

        // получаем отзывы для товара
        $reviewsData = [];
        if (\App::config()->product['reviewEnabled']) {
            \RepositoryManager::review()->prepareData($product->getUi(), 'user', 0, \Model\Review\Repository::NUM_REVIEWS_ON_PAGE, function($data) use(&$reviewsData) {
                if ((bool)$data) {
                    $reviewsData = (array)$data;
                }
            });
        }

        $accessoriesId =  $product->getAccessoryId();
        $partsId = [];

        foreach ($product->getKit() as $part) {
            $partsId[] = $part->getId();
        }

        $productsCollection = [];
        if ((bool)$accessoriesId || (bool)$partsId) {
            // если аксессуары уже получены в filterAccessoryId для них запрос не делаем
            $ids = !empty($accessoryItems) ? $partsId : array_merge($accessoriesId, $partsId);
            $chunckedIds = array_chunk($ids, \App::config()->coreV2['chunk_size']);

            foreach ($chunckedIds as $i => $chunk) {
                $repository->prepareCollectionById($chunk, $region, function($data) use(&$productsCollection, $i) {
                    foreach ((array)$data as $item) {
                        if (empty($item['id'])) continue;

                        $productsCollection[$i][] = new \Model\Product\Entity($item);
                    }
                });
            }
        }

        $isUserSubscribedToEmailActions = false;
        if ($user->getEntity()) {
            $client->addQuery(
                'subscribe/get',
                ['token' => $user->getEntity()->getToken()],
                [],
                function($data) use(&$isUserSubscribedToEmailActions) {
                    foreach ($data as $item) {
                        $entity = new \Model\Subscribe\Entity($item);
                        if (1 == $entity->getChannelId() && 'email' === $entity->getType() && $entity->getIsConfirmed()) {
                            $isUserSubscribedToEmailActions = true;
                            break;
                        }
                    }
                }
            );
        }

        $actionChannelName = '';
        $client->addQuery(
            'subscribe/get-channel',
            [],
            [],
            function ($data) use (&$actionChannelName) {
                if (is_array($data)) {
                    foreach ($data as $channel) {
                        $channel = new \Model\Subscribe\Channel\Entity($channel);
                        if (1 == $channel->getId()) {
                            $actionChannelName = $channel->getName();
                            break;
                        }
                    }
                }
            },
            function(\Exception $e) {
                \App::exception()->remove($e);
            }
        );

        /** @var Trustfactor[] $trustfactors */
        $trustfactors = [];
        \App::scmsClient()->addQuery(
            'product/get-description/v1',
            ['uids' => [$product->getUi()], 'trustfactor' => 1, 'seo' => 1, 'media' => 1],
            [],
            function($data) use(&$trustfactors, $product) {
                if (!isset($data['products'][$product->getUi()])) {
                    return;
                }
    
                $data = $data['products'][$product->getUi()];
    
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
            },
            function(\Exception $e) {
                \App::logger()->error(['error' => $e, 'sender' => __FILE__ . ' ' .  __LINE__], ['controller']);
                \App::exception()->remove($e);
            }
        );

        // SITE-5035
//        $queryParams = [];
//        if ($rrUserId = $request->cookies->get('rrpusid')) {
//            $queryParams['rrUserId'] = $rrUserId;
//        }
//
//        $similarProductIds = [];
//        \App::retailrocketClient()->addQuery('Recomendation/UpSellItemToItems', $product->getId(), $queryParams, [], function($data) use (&$similarProductIds) {
//            if (is_array($data)) {
//                $similarProductIds = array_slice($data, 0, 10);
//            }
//        }, null, 0.15);

        // выполнение 3-го пакета запросов
        \App::curl()->execute();

        // SITE-5035
        $similarProducts = [];
//        $repository->prepareCollectionById($similarProductIds, $region, function($data) use ($similarProductIds, &$similarProducts) {
//            if (!is_array($data)) {
//                $data = [];
//            }
//
//            foreach ($data as $item) {
//                if (is_array($item)) {
//                    $similarProducts[] = new \Model\Product\Entity($item);
//                }
//            }
//        });
//
//        \App::curl()->execute();

        // получаем рейтинги
        $reviewsDataSummary = [];
        if (\App::config()->product['reviewEnabled']) {
            $reviewsDataSummary = \RepositoryManager::review()->getReviewsDataSummary($reviewsData);
        }

        if ($lifeGiftProduct && !($lifeGiftProduct->getLabel() && (\App::config()->lifeGift['labelId'] === $lifeGiftProduct->getLabel()->getId()))) {
            $lifeGiftProduct = null;
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

        $kitProducts = [];
        $productRepository = \RepositoryManager::product();
        $productRepository->setEntityClass('\Model\Product\Entity');

        /* Набор пакеты */
        if ((bool)$product->getKit()) {
            $kitProducts = $productRepository->getKitProducts($product);
        }

        /*
        if ($categoryClass) {
            $controller = '\\Controller\\'.ucfirst($categoryClass).'\\Product\\IndexAction';
            return (new $controller())->executeDirect($product, $catalogJson);
        }
        */

        // фильтруем аксессуары согласно разрешенным в json категориям
        // и получаем уникальные категории-родители аксессуаров
        // для построения меню категорий в блоке аксессуаров
        // сразу сохраняем аксессуары, чтобы позже не делать для них повторный запрос
        $accessoryItems = [];
        $accessoryCategory = array_map(function($accessoryGrouped){
            return $accessoryGrouped['category'];
        }, \Model\Product\Repository::filterAccessoryId($product, $accessoryItems, null, \App::config()->product['itemsInAccessorySlider'] * 36, $catalogJson));
        if ((bool)$accessoryCategory) {
            $firstAccessoryCategory = new \Model\Product\Category\Entity();
            $firstAccessoryCategory->setId(0);
            $firstAccessoryCategory->setName('Популярные аксессуары');
            array_unshift($accessoryCategory, $firstAccessoryCategory);
        }

        $accessoriesId =  array_slice($product->getAccessoryId(), 0, $accessoryCategory ? \App::config()->product['itemsInAccessorySlider'] * 36 : \App::config()->product['itemsInSlider'] * 6);
        $additionalData = [];
        $accessories = array_flip($accessoriesId);
        $kit = array_flip($partsId);

        if ((bool)$accessoriesId || (bool)$partsId) {
            try {
                $result = [];
                foreach ($productsCollection as $chunk) {
                    $result = array_merge($result, $chunk);
                }

                // если аксессуары уже получены в filterAccessoryId для них запрос не делаем
                if(!empty($accessoryItems)) {
                    $products = array_merge($accessoryItems, $result);
                } else {
                    $products = \RepositoryManager::review()->addScores($result);
                }
            } catch (\Exception $e) {
                \App::exception()->add($e);
                \App::logger()->error($e);

                $products = [];
                $accessories = [];
                $kit = [];
            }

            $accessoriesCount = 1;
            foreach ($products as $item) {
                if (isset($accessories[$item->getId()])) {
                    $accessoriesCount++;
                    $accessories[$item->getId()] = $item;
                }
                if (isset($kit[$item->getId()])) $kit[$item->getId()] = $item;
            }
        }

        // фильтрация связанных товаров
        $notEmpty = function ($related) use ($product) {
            $return = $related instanceof \Model\Product\BasicEntity;
            if (!$return) {
                \App::logger()->error(sprintf('Для товара #%s не найден связанный товар', $product->getId()));
            }

            return $return;
        };
        $accessories = array_filter($accessories, $notEmpty);
        $kit = array_filter($kit, $notEmpty);

        $creditData = (new \Controller\Product\IndexAction())->getDataForCredit($product);

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
        if ((bool)$quantityByShop) {
            \RepositoryManager::shop()->prepareCollectionById(
                array_keys($quantityByShop),
                function($data) use (&$shopStates, &$quantityByShop) {
                    foreach ($data as $item) {
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
            );
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
        $page->setParam('additionalData', $additionalData);
        $page->setParam('creditData', $creditData);
        $page->setParam('shopStates', $shopStates);
        $page->setParam('reviewsData', $reviewsData);
        $page->setParam('reviewsDataSummary', $reviewsDataSummary);
        $page->setParam('categoryClass', $categoryClass);
        $page->setParam('useLens', $useLens);
        $page->setParam('catalogJson', $catalogJson);
        $page->setParam('trustfactors', $trustfactors);
        $page->setParam('deliveryData', (new \Controller\Product\DeliveryAction())->getResponseData([['id' => $product->getId()]], $region->getId()));
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
}