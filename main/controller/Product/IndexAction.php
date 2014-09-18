<?php

namespace Controller\Product;

class IndexAction {
    use TrustfactorsTrait;

    /**
     * @param string        $productPath
     * @param \Http\Request $request
     * @return \Http\RedirectResponse|\Http\Response
     * @throws \Exception\NotFoundException
     */
    public function execute($productPath, \Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $client = \App::coreClientV2();
        $user = \App::user();
        $repository = \RepositoryManager::product();

        $productToken = explode('/', $productPath);
        $productToken = end($productToken);

        // подготовка 1-го пакета запросов

        // запрашиваем текущий регион, если есть кука региона
        $regionConfig = [];
        if ($user->getRegionId()) {
            \App::dataStoreClient()->addQuery("region/{$user->getRegionId()}.json", [], function($data) use (&$regionConfig) {
                if((bool)$data) {
                    $regionConfig = $data;
                }
            });

            \RepositoryManager::region()->prepareEntityById($user->getRegionId(), function($data) {
                $data = reset($data);
                if ((bool)$data) {
                    \App::user()->setRegion(new \Model\Region\Entity($data));
                }
            });
        }

        // запрашиваем список регионов для выбора
        $regionsToSelect = [];
        \RepositoryManager::region()->prepareShownInMenuCollection(function($data) use (&$regionsToSelect) {
            foreach ($data as $item) {
                $regionsToSelect[] = new \Model\Region\Entity($item);
            }
        });

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
            $data = reset($data);

            if ((bool)$data) {
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

        // категории продукта
        $productCategoryTokens = array_map(function($category){
            return $category->getToken();
        }, $product->getCategory());

        // получаем catalog json
        $catalogJson = [];
        \App::scmsClient()->addQuery('category/get', ['uid' => $product->getLastCategory()->getUi(), 'geo_id' => $user->getRegion()->getId()], [], function ($data) use (&$catalogJson) {
            $catalogJson = \RepositoryManager::productCategory()->convertScmsDataToOldCmsData($data);
        });

        // настройки товара
        $productConfig = [];
        $dataStore = \App::dataStoreClient();
        $dataStore->addQuery(sprintf('product/%s.json', $product->getToken()), [], function ($data) use (&$productConfig) {
            if (is_array($data)) $productConfig = $data;
        });

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
                    foreach ($data as $item) {
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

        // выполнение 3-го пакета запросов
        \App::curl()->execute();
        $catalogJson = array_merge_recursive($catalogJson, $productConfig);

        // получаем рейтинги
        $reviewsDataSummary = [];
        if (\App::config()->product['reviewEnabled']) {
            $reviewsDataSummary = \RepositoryManager::review()->getReviewsDataSummary($reviewsData);
        }

        if ($lifeGiftProduct && !($lifeGiftProduct->getLabel() && (\App::config()->lifeGift['labelId'] === $lifeGiftProduct->getLabel()->getId()))) {
            $lifeGiftProduct = null;
        }

        // SITE-3982
        // Трастфактор "Спасибо от Сбербанка" не должен отображаться на карточке товара от Связного
        if (is_array($product->getPartnersOffer()) && count($product->getPartnersOffer()) !== 0 && isset($catalogJson['trustfactor_right']) && $catalogJson['trustfactor_right']) {
            $catalogJson['trustfactor_right'] = array_filter($catalogJson['trustfactor_right'], function ($trustfactor) {
                return 'trust_sber' === $trustfactor ? false : true;
            });
        }

        $trustfactors = $this->getTrustfactors($catalogJson, $productCategoryTokens);

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

        // Если набор, то получим $productLine
        $productLine = $product->getLine();

        $line = null;
        $parts = [];
        $kitProducts = [];
        $relatedKits = [];
        $productRepository = \RepositoryManager::product();
        $productRepository->setEntityClass('\Model\Product\Entity');

        /* Набор пакеты */
        if ((bool)$product->getKit()) {
            $restParts = [];

            // Получим основные товары набора
            $productPartsIds = [];
            foreach ($product->getKit() as $part) {
                $productPartsIds[] = $part->getId();
            }

            // Если товар находится в какой-либо линии, то запросим остальные продукты линии
            if ($productLine instanceof \Model\Product\Line\Entity ) {
                $line = \RepositoryManager::line()->getEntityByToken($productLine->getToken());
                $restPartsIds = array_diff($line->getProductId(), $productPartsIds);
            }

            // Получим сущности по id
            try {
                $parts = $productRepository->getCollectionById($productPartsIds);
                if (isset($restPartsIds) && count($restPartsIds) > 0) {
                    $restParts = $productRepository->getCollectionById($restPartsIds);
                } else {
                    $restParts = [];
                }
            } catch (\Exception $e) {
                \App::exception()->add($e);
                \App::logger()->error($e);
            }

            // Приготовим набор для отображения на сайте
            $kitProducts = $this->prepareKit($parts, $restParts, $product, $region);
        }

        // Если у товара есть линия, то получим киты, в которые он входит
        if ($productLine instanceof \Model\Product\Line\Entity ) {
            try {
                $line = \RepositoryManager::line()->getEntityByToken($productLine->getToken());
                if (!$line || !$line instanceof \Model\Line\Entity) {
                    throw new \Exception(sprintf('Не получена линия %s', $productLine->getToken()));
                }

                $lineKits = $productRepository->getCollectionById($line->getKitId());
                $relatedKitsIds = [];
                foreach ($lineKits as $kit) {
                    if (in_array($product->getId(), array_map(function($v){ return $v->getId(); }, $kit->getKit()))) $relatedKitsIds[] = $kit->getId();
                }
                if ((bool)$relatedKitsIds) $relatedKits = $productRepository->getCollectionById($relatedKitsIds);
            } catch (\Exception $e) {
                \App::exception()->add($e);
                \App::logger()->error($e);
            }
        }

        /*
        if ($categoryClass) {
            $controller = '\\Controller\\'.ucfirst($categoryClass).'\\Product\\IndexAction';
            return (new $controller())->executeDirect($product, $regionsToSelect, $catalogJson);
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
                    $additionalData[$item->getId()] = \Kissmetrics\Manager::getProductEvent($item, $accessoriesCount, 'Accessorize');
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

        $creditData = $this->getDataForCredit($product);

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

        try {
            $productVideos = \RepositoryManager::productVideo()->getCollectionByProduct($product);
        } catch (\Exception $e) {
            \App::logger()->error($e);
            $productVideos = [];
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


        $page = new \View\Product\IndexPage();
        $page->setParam('renderer', \App::closureTemplating());
        $page->setParam('regionsToSelect', $regionsToSelect);
        $page->setParam('product', $product);
        $page->setParam('lifeGiftProduct', $lifeGiftProduct);
        $page->setParam('productVideos', $productVideos);
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
        $page->setParam('trustfactorTop', $trustfactors['top']);
        $page->setParam('trustfactorMain', $trustfactors['main']);
        $page->setParam('trustfactorRight', $trustfactors['right']);
        $page->setParam('trustfactorContent', $trustfactors['content']);
        $page->setParam('line', $line);
        $page->setParam('deliveryData', (new \Controller\Product\DeliveryAction())->getResponseData([['id' => $product->getId()]], $region->getId()));
        $page->setParam('isUserSubscribedToEmailActions', $isUserSubscribedToEmailActions);
        $page->setGlobalParam('from', $request->get('from') ? $request->get('from') : null);
        $page->setParam('viewParams', [
            'showSideBanner' => \Controller\ProductCategory\Action::checkAdFoxBground($catalogJson)
        ]);
        $page->setGlobalParam('isTchibo', ($product->getMainCategory() && 'Tchibo' === $product->getMainCategory()->getName()));
        $page->setGlobalParam('addToCartJS', $addToCartJS);
        
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
    protected function getDataForCredit(\Model\Product\Entity $product) {
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
        \RepositoryManager::paymentGroup()->prepareCollection($region,
            [
                'is_corporative' => false,
                'is_credit'      => $is_credit,
            ],
            [
                'product_list'   => [$product->getId() => ['id' => $product->getId(), 'quantity' => (($cart->getQuantityByProduct($product->getId()) > 0) ? $cart->getQuantityByProduct($product->getId()) : 1)]],
            ],
            function($data) use (&$hasCreditPaymentMethod) {
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
            },
            function($e){
                \App::exception()->remove($e);
                \App::logger()->error($e);
            }
        );
        \App::curl()->execute();

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

    /**
     * Подготовка данных для набора продуктов
     * @var array $products
     * @var array $restProducts
     * @var \Model\Product\Entity $product
     * @var \Model\Region\Entity $region
     */
    private function prepareKit($products, $restProducts, $mainProduct, $region) {
        $result = [];

        foreach (array('baseLine' => $products, 'restLine' => $restProducts) as $lineName => $products) {

            foreach ($products as $key => $product) {
                $id = $product->getId();
                $result[$id]['id'] = $id;
                $result[$id]['name'] = $product->getName();
                $result[$id]['article'] = $product->getArticle();
                $result[$id]['token'] = $product->getToken();
                $result[$id]['url'] = $product->getLink();
                $result[$id]['image'] = $product->getImageUrl();
                $result[$id]['product'] = $product;
                $result[$id]['price'] = $product->getPrice();
                $result[$id]['lineName'] = $lineName;
                $result[$id]['height'] = '';
                $result[$id]['width'] = '';
                $result[$id]['depth'] = '';
                $result[$id]['deliveryDate'] = '';

                // добавляем размеры
                $dimensionsTranslate = [
                    'Высота' => 'height',
                    'Ширина' => 'width',
                    'Глубина' => 'depth'
                ];
                if ($product->getProperty()) {
                    foreach ($product->getProperty() as $property) {
                        if (in_array($property->getName(), array('Высота', 'Ширина', 'Глубина'))) {
                            $result[$id][$dimensionsTranslate[$property->getName()]] = $property->getValue();
                        }
                    }
                }
            }

        }

        foreach ($result as &$value) {
            $value['count'] = 0;
        }

        foreach ($mainProduct->getKit() as $kitPart) {
            if (isset($result[$kitPart->getId()])) $result[$kitPart->getId()]['count'] = $kitPart->getCount();
        }

        $deliveryItems = [];
        foreach ($result as $item) {
            $deliveryItems[] = array(
                'id'    => $item['product']->getId(),
                'quantity' => isset($item['count']) ? $item['count'] : 1
            );
        }

        $deliveryData = (new \Controller\Product\DeliveryAction())->getResponseData($deliveryItems, $region->getId());

        if ($deliveryData['success']) {
            foreach ($deliveryData['product'] as $product) {
                $id = $product['id'];
                $date = $product['delivery'][0]['date']['value'];
                $result[$id]['deliveryDate'] = $date;
            }

        }

        return $result;
    }


}