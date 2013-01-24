<?php

namespace Controller\Product;

class IndexAction {
    public function execute($productPath, \Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $client = \App::coreClientV2();
        $user = \App::user();
        $repository = \RepositoryManager::product();

        $productToken = explode('/', $productPath);
        $productToken = end($productToken);

        // подготовка 1-го пакета запросов

        // запрашиваем пользователя, если он авторизован
        /*if ($user->getToken()) {
            \RepositoryManager::user()->prepareEntityByToken($user->getToken(), function($data) {
                if ((bool)$data) {
                    \App::user()->setEntity(new \Model\User\Entity($data));
                }
            }, function (\Exception $e) {
                \App::exception()->remove($e);
                $token = \App::user()->removeToken();
                throw new \Exception\AccessDeniedException(sprintf('Время действия токена %s истекло', $token));
            });
        }*/

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
        $regionsToSelect = array();
        \RepositoryManager::region()->prepareShowInMenuCollection(function($data) use (&$regionsToSelect) {
            foreach ($data as $item) {
                $regionsToSelect[] = new \Model\Region\Entity($item);
            }
        });

        // выполнение 1-го пакета запросов
        $client->execute();

        $region = $user->getRegion();

        // подготовка 2-го пакета запросов

        // запрашиваем рутовые категории
        $rootCategories = array();
        \RepositoryManager::productCategory()->prepareRootCollection($region, function($data) use(&$rootCategories) {
            foreach ($data as $item) {
                $rootCategories[] = new \Model\Product\Category\Entity($item);
            }
        });

        // запрашиваем товар по токену
        /** @var $product \Model\Product\Entity */
        $product = null;
        \RepositoryManager::product()->prepareEntityByToken($productToken, $region, function($data) use (&$product) {
            $data = reset($data);
            if ((bool)$data) {
                $product = new \Model\Product\Entity($data);
            }
        });

        // выполнение 2-го пакета запросов
        $client->execute();

        if (!$product) {
            throw new \Exception\NotFoundException(sprintf('Товар @%s не найден.', $productToken));
        }

        if ($product->getConnectedProductsViewMode() == $product::DEFAULT_CONNECTED_PRODUCTS_VIEW_MODE) {
            $showRelatedUpper = false;
        } else {
            $showRelatedUpper = true;
        }

        $accessoriesId =  array_slice($product->getAccessoryId(), 0, \App::config()->product['itemsInSlider'] * 2);
        $relatedId = array_slice($product->getRelatedId(), 0, \App::config()->product['itemsInSlider'] * 2);
        $partsId = array();

        foreach ($product->getKit() as $part) {
            $partsId[] = $part->getId();
        }

        $accessories = array_flip($accessoriesId);
        $related = array_flip($relatedId);
        $kit = array_flip($partsId);

        if ((bool)$accessoriesId || (bool)$relatedId || (bool)$partsId) {
            try {
                $products = $repository->getCollectionById(array_merge($accessoriesId, $relatedId, $partsId));
            } catch (\Exception $e) {
                \App::exception()->add($e);
                \App::logger()->error($e);

                $products = array();
                $accessories = array();
                $related = array();
                $kit = array();
            }

            foreach ($products as $item) {
                if (isset($accessories[$item->getId()])) $accessories[$item->getId()] = $item;
                if (isset($related[$item->getId()])) $related[$item->getId()] = $item;
                if (isset($kit[$item->getId()])) $kit[$item->getId()] = $item;
            }
        }
        $dataForCredit = $this->getDataForCredit($product);

        $shopsWithQuantity = array();
        //загружаем магазины, если товар доступен только на витрине
        if (!$product->getIsBuyable() && $product->getState()->getIsShop()) {
            $quantityByShop = array();
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

                    $shopsWithQuantity = array();
                }

            }
        }

        $page = new \View\Product\IndexPage();
        $page->setParam('regionsToSelect', $regionsToSelect);
        $page->setParam('rootCategories', $rootCategories);
        $page->setParam('product', $product);
        $page->setParam('title', $product->getName());
        $page->setParam('showRelatedUpper', $showRelatedUpper);
        $page->setParam('showAccessoryUpper', !$showRelatedUpper);
        $page->setParam('accessories', $accessories);
        $page->setParam('related', $related);
        $page->setParam('kit', $kit);
        $page->setParam('dataForCredit', $dataForCredit);
        $page->setParam('shopsWithQuantity', $shopsWithQuantity);

        return new \Http\Response($page->show());
    }

    /**
     * Собирает в массив данные, необходимые для плагина online кредитовария // скопировано из symfony
     *
     * @param $product
     * @return array
     */
    private function getDataForCredit(\Model\Product\Entity $product) {
        $result = array();

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