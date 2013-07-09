<?php

namespace Controller\Product;

class IndexAction {
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
        if ($user->getRegionId()) {
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

        $region = $user->getRegion();

        // подготовка 2-го пакета запросов

        // TODO: запрашиваем меню

        // запрашиваем товар по токену
        /** @var $product \Model\Product\Entity */
        $product = null;
        $productExpanded = null;
        $dataR = null;
        \RepositoryManager::product()->prepareEntityByToken($productToken, $region, function($data) use (&$product, &$productExpanded) {
            $data = reset($data);

            if ((bool)$data) {
                $productExpanded = new \Model\Product\ExpandedEntity($data);
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

        // получаем catalog json для родительской категории
        $productCategories = $product->getCategory();
        $catalogJson = \RepositoryManager::productCategory()->getCatalogJson(array_pop($productCategories));

        // если в catalogJson'e указан category_class, то обрабатываем запрос соответствующим контроллером
        if(!empty($catalogJson['category_class'])) {
            $controller = '\\Controller\\'.ucfirst($catalogJson['category_class']).'\\Product\\IndexAction';
            return (new $controller())->executeDirect($product, $regionsToSelect, $catalogJson);
        }

        // получаем отзывы для товара
        $reviewsData = \RepositoryManager::review()->getReviews($product->getId(), 'user');
        $reviewsDataPro = \RepositoryManager::review()->getReviews($product->getId(), 'pro');
        $reviewsDataSummary = \RepositoryManager::review()->prepareReviewsDataSummary($reviewsData, $reviewsDataPro);

        // фильтруем аксессуары согласно разрешенным в json категориям
        // и получаем уникальные категории-родители аксессуаров
        // для построения меню категорий в блоке аксессуаров
        // сразу сохраняем аксессуары, чтобы позже не делать для них повторный запрос
        $accessoryItems = [];
        $accessoryCategory = array_map(function($accessoryGrouped){
            return $accessoryGrouped['category'];
        }, \Model\Product\Repository::filterAccessoryId($product, $accessoryItems, null, \App::config()->product['itemsInAccessorySlider'] * 6));

        $accessoriesId =  array_slice($product->getAccessoryId(), 0, $accessoryCategory ? \App::config()->product['itemsInAccessorySlider'] * 6 : \App::config()->product['itemsInSlider'] * 6);
        $relatedId = array_slice($product->getRelatedId(), 0, \App::config()->product['itemsInSlider'] * 2);
        $partsId = [];

        foreach ($product->getKit() as $part) {
            $partsId[] = $part->getId();
        }

        $additionalData = [];
        $accessories = array_flip($accessoriesId);
        $related = array_flip($relatedId);
        $kit = array_flip($partsId);

        if ((bool)$accessoriesId || (bool)$relatedId || (bool)$partsId) {
            try {
                // если аксессуары уже получены в filterAccessoryId для них запрос не делаем
                if(!empty($accessoryItems)) {
                    $products = $repository->getCollectionById(array_merge($relatedId, $partsId));
                    $products = array_merge($accessoryItems, $products);
                } else {
                    $products = $repository->getCollectionById(array_merge($accessoriesId, $relatedId, $partsId));
                }
            } catch (\Exception $e) {
                \App::exception()->add($e);
                \App::logger()->error($e);

                $products = [];
                $accessories = [];
                $related = [];
                $kit = [];
            }

            $accessoriesCount = 1;
            $relatedCount = 1;
            foreach ($products as $item) {
                if (isset($accessories[$item->getId()])) {
                    $additionalData[$item->getId()] = \Kissmetrics\Manager::getProductEvent($item, $accessoriesCount, 'Accessorize');
                    $accessoriesCount++;
                    $accessories[$item->getId()] = $item;
                }
                if (isset($related[$item->getId()])) {
                    $additionalData[$item->getId()] = \Kissmetrics\Manager::getProductEvent($item, $relatedCount, 'Also Bought');
                    $relatedCount++;
                    $related[$item->getId()] = $item;
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
        $related = array_filter($related, $notEmpty);
        $kit = array_filter($kit, $notEmpty);

        $creditData = $this->getDataForCredit($product);

        /** @var $shopStates \Model\Product\ShopState\Entity[] */
        $shopStates = [];
        //загружаем магазины, если товар доступен только на витрине
        if (!$product->getIsBuyable() && $product->getState()->getIsShop()) {
            $quantityByShop = [];
            foreach ($product->getStock() as $stock) {
                $quantityShowroom = (int)$stock->getQuantityShowroom();
                $quantity = (int)$stock->getQuantity();
                $shopId = $stock->getShopId();
                if ((0 < $quantity + $quantityShowroom) && !empty($shopId)) {
                    $quantityByShop[$shopId] = array(
                        'quantity' => $quantity,
                        'quantityShowroom' => $quantityShowroom,
                    );
                }
            }
            if ((bool)$quantityByShop) {
                \RepositoryManager::shop()->prepareCollectionById(
                    array_keys($quantityByShop),
                    function($data) use (&$shopStates, &$quantityByShop) {
                        foreach ($data as $item) {
                            $shop = new \Model\Shop\Entity($item);

                            $shopState = new \Model\Product\ShopState\Entity();
                            $shopState->setShop($shop);
                            $shopState->setQuantity(isset($quantityByShop[$shop->getId()]['quantity']) ? $quantityByShop[$shop->getId()]['quantity'] : 0);
                            $shopState->setQuantityInShowroom(isset($quantityByShop[$shop->getId()]['quantityShowroom']) ? $quantityByShop[$shop->getId()]['quantityShowroom'] : 0);

                            $shopStates[] = $shopState;
                        }
                    }
                );
            }
        }

        try {
            $productVideos = \RepositoryManager::productVideo()->getCollectionByProduct($product);
        } catch (\Exception $e) {
            \App::logger()->error($e);
            $productVideos = [];
        }

        $page = new \View\Product\IndexPage();
        $page->setParam('renderer', \App::closureTemplating());
        $page->setParam('regionsToSelect', $regionsToSelect);
        $page->setParam('product', $product);
        $page->setParam('productExpanded', $productExpanded);
        $page->setParam('productVideos', $productVideos);
        $page->setParam('title', $product->getName());
        $page->setParam('accessories', $accessories);
        $page->setParam('accessoryCategory', $accessoryCategory);
        $page->setParam('related', $related);
        $page->setParam('kit', $kit);
        $page->setParam('additionalData', $additionalData);
        $page->setParam('creditData', $creditData);
        $page->setParam('shopStates', $shopStates);
        $page->setParam('myThingsData', [
            'EventType' => 'MyThings.Event.Visit',
            'Action'    => '1010',
            'ProductId' => $product->getId(),
        ]);
        $page->setParam('reviewsData', $reviewsData);
        $page->setParam('reviewsDataPro', $reviewsDataPro);
        $page->setParam('reviewsDataSummary', $reviewsDataSummary);

        return new \Http\Response($page->show());
    }

    /**
     * Собирает в массив данные, необходимые для плагина online кредитовария // скопировано из symfony
     *
     * @param $product
     * @return array
     */
    protected function getDataForCredit(\Model\Product\Entity $product) {
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

        $dataForCredit = array(
            'price'        => $product->getPrice(),
            //'articul'      => $product->getArticle(),
            'name'         => $product->getName(),
            'count'        => $cart->getQuantityByProduct($product->getId()),
            'product_type' => $productType,
            'session_id'   => session_id()
        );
        $result['creditIsAllowed'] = (bool) (($product->getPrice() * (($cart->getQuantityByProduct($product->getId()) > 0)? $cart->getQuantityByProduct($product->getId()) : 1)) > \App::config()->product['minCreditPrice']);
        $result['creditData'] = json_encode($dataForCredit);

        return $result;
    }


}