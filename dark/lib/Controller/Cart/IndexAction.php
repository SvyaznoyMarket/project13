<?php

namespace Controller\Cart;

class IndexAction {
    public function execute(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $client = \App::coreClientV2();
        $user = \App::user();
        $cart = $user->getCart();

        // подготовка 1-го пакета запросов

        // запрашиваем пользователя, если он авторизован
        if ($user->getToken()) {
            $client->addQuery('user/get', array('token' => $user->getToken()), array(), function($data) {
                if ((bool)$data) {
                    \App::user()->setEntity(new \Model\User\Entity($data));
                }
            });
        }

        // запрашиваем текущий регион, если есть кука региона
        if ($user->getRegionId()) {
            \RepositoryManager::getRegion()->prepareEntityById($user->getRegionId(), function($data) {
                $data = reset($data);
                if ((bool)$data) {
                    \App::user()->setRegion(new \Model\Region\Entity($data));
                }
            });
        }

        // запрашиваем список регионов для выбора
        $shopAvailableRegions = array();
        \RepositoryManager::getRegion()->prepareShopAvailableCollection(function($data) use (&$shopAvailableRegions) {
            foreach ($data as $item) {
                $shopAvailableRegions[] = new \Model\Region\Entity($item);
            }
        });

        // выполнение 1-го пакета запросов
        $client->execute();

        $region = $user->getRegion();

        // подготовка 2-го пакета запросов

        // запрашиваем рутовые категории
        $rootCategories = array();
        \RepositoryManager::getProductCategory()->prepareRootCollection($region, function($data) use(&$rootCategories) {
            foreach ($data as $item) {
                $rootCategories[] = new \Model\Product\Category\Entity($item);
            }
        });

        $cartProductsById = $cart->getProducts();
        $cartServicesById = $cart->getServices();

        $productIds = array_keys($cartProductsById);
        $serviceIds = array_keys($cartServicesById);

        $products = array();
        $services = array();

        // запрашиваем список товаров
        if ((bool)$productIds) {
            \RepositoryManager::getProduct()->prepareCollectionById($productIds, $region, function($data) use(&$products, $cartProductsById) {
                foreach ($data as $item) {
                    $products[] = new \Model\Product\CartEntity($item);
                }
            });
        }

        // запрашиваем список услуг
        if ((bool)$serviceIds) {
            \RepositoryManager::getService()->prepareCollectionById($serviceIds, $region, function($data) use(&$services, $cartServicesById) {
                foreach ($data as $item) {
                    $services[] = new \Model\Product\Service\Entity($item);
                }
            });
        }

        // выполнение 2-го пакета запросов
        $client->execute();

        $page = new \View\Cart\IndexPage();
        $page->setParam('shopAvailableRegions', $shopAvailableRegions);
        $page->setParam('rootCategories', $rootCategories);
        $page->setParam('selectCredit', 1 == $request->cookies->get('credit_on'));
        $page->setParam('products', $products);
        $page->setParam('services', $services);
        $page->setParam('cartProductsById', $cartProductsById);
        $page->setParam('cartServicesById', $cartServicesById);

        return new \Http\Response($page->show());
    }
}