<?php

namespace Controller\User;

class OrderAction {
    public function __construct() {
        if (!\App::user()->getToken()) {
            throw new \Exception\AccessDeniedException();
        }
    }

    public function execute(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $client = \App::coreClientV2();
        $user = \App::user();

        // подготовка 1-го пакета запросов

        // запрашиваем пользователя, если он авторизован
        if ($user->getToken()) {
            \RepositoryManager::getUser()->prepareEntityByToken($user->getToken(), function($data) {
                if ((bool)$data) {
                    \App::user()->setEntity(new \Model\User\Entity($data));
                }
            }, function (\Exception $e) {
                \App::$exception = null;
                $token = \App::user()->removeToken();
                throw new \Exception\AccessDeniedException(sprintf('Время действия токена %s истекло', $token));
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
        $regionsToSelect = array();
        \RepositoryManager::getRegion()->prepareShowInMenuCollection(function($data) use (&$regionsToSelect) {
            foreach ($data as $item) {
                $regionsToSelect[] = new \Model\Region\Entity($item);
            }
        });

        // выполнение 1-го пакета запросов
        $client->execute();

        $region = $user->getRegion();

        // способы получения заказа
        $deliveryTypesById = array();
        foreach (\RepositoryManager::getDeliveryType()->getCollection() as $deliveryType) {
            $deliveryTypesById[$deliveryType->getId()] = $deliveryType;
        }

        // подготовка 2-го пакета запросов

        // запрашиваем рутовые категории
        $rootCategories = array();
        \RepositoryManager::getProductCategory()->prepareRootCollection($region, function($data) use(&$rootCategories) {
            foreach ($data as $item) {
                $rootCategories[] = new \Model\Product\Category\Entity($item);
            }
        });

        // запрашиваем заказы пользователя
        /** @var $orders \Model\Order\Entity[] */
        $orders = array();
        \RepositoryManager::getOrder()->prepareCollectionByUserToken($user->getToken(), function($data) use(&$orders) {
            foreach ($data as $item) {
                $orders[] = new \Model\Order\Entity($item);
            }
            // сортировка по дате desc
            /** @var $orders \Model\Order\Entity[] */
            $orders = array_reverse($orders);
        });

        // выполнение 2-го пакета запросов
        $client->execute();

        // товары и услуги
        $productsById = array();
        $servicesById = array();
        foreach ($orders as $order) {
            foreach ($order->getProduct() as $orderProduct) {
                $productsById[$orderProduct->getId()] = null;
            }
            foreach ($order->getService() as $orderService) {
                $servicesById[$orderService->getId()] = null;
            }
        }

        // подготовка 3-го пакета запросов

        // методы оплаты
        $paymentMethodsById = array();
        \RepositoryManager::getPaymentMethod()->prepareCollection(
            $region->getId() == \App::config()->region['defaultId'] ? $region : \RepositoryManager::getRegion()->getDefaultEntity(),
            function($data) use(&$paymentMethodsById) {
                foreach($data as $item){
                    $paymentMethodsById[$item['id']] = new \Model\PaymentMethod\Entity($item);
                }
            }
        );

        // товары
        if ((bool)$productsById) {
            \RepositoryManager::getProduct()->prepareCollectionById(array_keys($productsById), $region, function($data) use(&$productsById) {
                foreach($data as $item){
                    $productsById[$item['id']] = new \Model\Product\CartEntity($item);
                }
            });
        }

        // услуги
        if ((bool)$servicesById) {
            \RepositoryManager::getService()->prepareCollectionById(array_keys($servicesById), $region, function($data) use(&$servicesById) {
                foreach($data as $item){
                    $servicesById[$item['id']] = new \Model\Product\Service\Entity($item);
                }
            });
        }

        if ((bool)$productsById || (bool)$servicesById) {
            // выполнение 3-го пакета запросов
            $client->execute();
        }

        $page = new \View\User\OrderPage();
        $page->setParam('regionsToSelect', $regionsToSelect);
        $page->setParam('rootCategories', $rootCategories);
        $page->setParam('deliveryTypesById', $deliveryTypesById);
        $page->setParam('paymentMethodsById', $paymentMethodsById);
        $page->setParam('orders', $orders);
        $page->setParam('productsById', $productsById);
        $page->setParam('servicesById', $servicesById);

        return new \Http\Response($page->show());
    }
}