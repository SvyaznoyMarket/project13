<?php

namespace Controller\Cart;

class IndexAction {
    /**
     * @param \Http\Request $request
     * @return \Http\Response
     */
    public function execute(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $client = \App::coreClientV2();
        $user = \App::user();
        $cart = $user->getCart();

        // подготовка 1-го пакета запросов

        // запрашиваем пользователя, если он авторизован
        /*if ($user->getToken()) {
            \RepositoryManager::region()->prepareEntityById($user->getRegionId(), function($data) {
                $data = reset($data);
                if ((bool)$data) {
                    \App::user()->setRegion(new \Model\Region\Entity($data));
                }
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
        $regionsToSelect = [];
        \RepositoryManager::region()->prepareShowInMenuCollection(function($data) use (&$regionsToSelect) {
            foreach ($data as $item) {
                $regionsToSelect[] = new \Model\Region\Entity($item);
            }
        });

        // выполнение 1-го пакета запросов
        $client->execute();

        $region = $user->getRegion();

        // подготовка 2-го пакета запросов

        // TODO: запрашиваем меню
        $cartProductsById = $cart->getProducts();
        $cartServicesById = $cart->getServices();

        $productIds = array_keys($cartProductsById);
        $serviceIds = array_keys($cartServicesById);

        /** @var $products \Model\Product\CartEntity[] */
        $products = [];
        /** @var $services \Model\Product\Service\Entity[] */
        $services = [];
        /** @var $products \Model\Product\Entity[] */
        $productEntities = [];

        // запрашиваем список товаров
        if ((bool)$productIds) {
            \RepositoryManager::product()->prepareCollectionById($productIds, $region, function($data) use(&$products, $cartProductsById, &$productEntities) {
                foreach ($data as $item) {
                    $products[] = new \Model\Product\CartEntity($item);
                    $productEntities[] = new \Model\Product\Entity($item);
                }
            });
        }

        // запрашиваем список услуг
        if ((bool)$serviceIds) {
            \RepositoryManager::service()->prepareCollectionById($serviceIds, $region, function($data) use(&$services, $cartServicesById) {
                foreach ($data as $item) {
                    $services[] = new \Model\Product\Service\Entity($item);
                }
            });
        }

        // выполнение 2-го пакета запросов
        $client->execute();

        // подготовка 3-го пакета запросов
        $hasAnyoneKit = false;
        $productKitsById = [];
        foreach ($products as $product) {
            $kitIds = array_map(function($kit) { /** @var $kit \Model\Product\Kit\Entity */ return $kit->getId(); }, $product->getKit());
            if ((bool)$kitIds) {
                $hasAnyoneKit = true;
                \RepositoryManager::product()->prepareCollectionById($kitIds, $region, function($data) use(&$productKitsById) {
                    foreach ($data as $item) {
                        $productKitsById[$item['id']] = new \Model\Product\CartEntity($item);
                    }
                });
            }
        }

        // выполнение 3-го пакета запросов
        if ($hasAnyoneKit) {
            $client->execute();
        }

        $page = new \View\Cart\IndexPage();
        $page->setParam('regionsToSelect', $regionsToSelect);
        $page->setParam('selectCredit', 1 == $request->cookies->get('credit_on'));
        $page->setParam('productEntities', $productEntities);
        $page->setParam('products', $products);
        $page->setParam('services', $services);
        $page->setParam('cartProductsById', $cartProductsById);
        $page->setParam('cartServicesById', $cartServicesById);
        $page->setParam('productKitsById', $productKitsById);

        return new \Http\Response($page->show());
    }
}