<?php

namespace Controller\Jewel\Product;

class IndexAction extends \Controller\Product\IndexAction {
    /**
     * Сейчас executeDirect() вызывается из \Controller\Product\IndexAction напрямую
     * Чтобы вызывать через роутинг, надо обращаться к этой функции
     *
     * @param string        $productPath
     * @param \Http\Request $request
     * @return \Http\RedirectResponse|\Http\Response
     * @throws \Exception\NotFoundException
     */
    public function prepareExecute($productPath, \Http\Request $request) {
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

        // подготовка 2-го пакета запросов

        // TODO: запрашиваем меню

        // запрашиваем товар по токену
        /** @var $product \Model\Product\Entity */
        $product = null;
        \RepositoryManager::product()->prepareEntityByToken($productToken, $region, function($data) use (&$product) {
            $data = reset($data);

            if ((bool)$data) {
                $product = new \Model\Product\Entity($data);
            }
        });

        // получаем отзывы для товара
        $reviewsData = [];
        $reviewsDataSummary = [];
        if (\App::config()->product['reviewEnabled']) {
            \RepositoryManager::review()->prepareData($product->getUi(), 'user', 0, \Model\Review\Repository::NUM_REVIEWS_ON_PAGE, function($data) use(&$reviewsData) {
                $reviewsData = (array)$data;
            });

            $reviewsDataSummary = \RepositoryManager::review()->getReviewsDataSummary($reviewsData);
        }

        // выполнение 2-го пакета запросов
        \App::curl()->execute();

        if (!$product) {
            throw new \Exception\NotFoundException(sprintf('Товар @%s не найден.', $productToken));
        }

        if ($request->getPathInfo() !== $product->getLink()) {
            return new \Http\RedirectResponse($product->getLink() . ((bool)$request->getQueryString() ? ('?' . $request->getQueryString()) : ''), 302);
        }

        // получаем catalog json для категории второго уровня (Пандора)
        $productCategories = $product->getCategory();
        array_shift($productCategories);
        $productCategory = reset($productCategories);
        $catalogJson = \RepositoryManager::productCategory()->getCatalogJson($productCategory);

        $reviews = [
            'reviewsData' => $reviewsData,
            'reviewsDataSummary' => $reviewsDataSummary,
        ];

        return $this->executeDirect($product, $regionsToSelect, $catalogJson, $reviews);
    }


    /**
     * @param \Model\Product\Entity    $product
     * @param \Model\Region\Entity[]   $regionsToSelect
     * @return \Http\Response
     */
    public function executeDirect($product, $regionsToSelect, $catalogJson, $reviews) {
        $repository = \RepositoryManager::product();

        if ($product->getConnectedProductsViewMode() == $product::DEFAULT_CONNECTED_PRODUCTS_VIEW_MODE) {
            $showRelatedUpper = false;
        } else {
            $showRelatedUpper = true;
        }

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

        $dataForCredit = $this->getDataForCredit($product);

        $shopsWithQuantity = [];
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
            if (count($quantityByShop)) {
                try {
                    $shops = \RepositoryManager::shop()->getCollectionById(array_keys($quantityByShop));
                    foreach ($shops as $shop) {
                        $shopsWithQuantity[] = array(
                            'shop' => $shop,
                            'quantity' => $quantityByShop[$shop->getId()]['quantity'],
                            'quantityShowroom' => $quantityByShop[$shop->getId()]['quantityShowroom'],
                        );
                    }
                } catch (\Exception $e) {
                    \App::exception()->add($e);
                    \App::logger()->error($e);

                    $shopsWithQuantity = [];
                }

            }
        }

        try {
            $productVideos = \RepositoryManager::productVideo()->getCollectionByProduct($product);
        } catch (\Exception $e) {
            \App::logger()->error($e);
            $productVideos = [];
        }

        $page = new \View\Jewel\Product\IndexPage();
        $page->setParam('regionsToSelect', $regionsToSelect);
        $page->setParam('product', $product);
        $page->setParam('productVideos', $productVideos);
        $page->setParam('title', $product->getName());
        $page->setParam('showRelatedUpper', $showRelatedUpper);
        $page->setParam('showAccessoryUpper', !$showRelatedUpper);
        $page->setParam('accessories', $accessories);
        $page->setParam('accessoryCategory', $accessoryCategory);
        $page->setParam('related', $related);
        $page->setParam('kit', $kit);
        $page->setParam('additionalData', $additionalData);
        $page->setParam('dataForCredit', $dataForCredit);
        $page->setParam('shopsWithQuantity', $shopsWithQuantity);
        $page->setParam('reviewsData', $reviews['reviewsData']);
        $page->setParam('reviewsDataSummary', $reviews['reviewsDataSummary']);
        $page->setParam('viewParams', [
            'showSideBanner' => \Controller\ProductCategory\Action::checkAdFoxBground($catalogJson)
        ]);

        $page->setParam('sprosikupiReviews', null);
        $page->setParam('shoppilotReviews', null);
        switch (\App::abTest()->getCase()->getKey()) {
            case 'reviews_sprosikupi':
                $client = new \SprosiKupi\Client(\App::config()->partners['SprosiKupi'], \App::logger());
                $page->setParam('sprosikupiReviews', $client->query($product->getId()));
                break;
            case 'reviews_shoppilot':
                $client = new \ShopPilot\Client(\App::config()->partners['ShopPilot'], \App::logger());
                $page->setParam('shoppilotReviews', $client->query($product->getId()));
                break;
        }

        return new \Http\Response($page->show());
    }

 
}