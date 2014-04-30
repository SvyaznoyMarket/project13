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
            \RepositoryManager::user()->prepareEntityByToken($user->getToken(), function($data) {
                if ((bool)$data) {
                    \App::user()->setEntity(new \Model\User\Entity($data));
                }
            }, function (\Exception $e) use (&$user) {
                \App::exception()->remove($e);
                throw new \Exception\AccessDeniedException(sprintf('Время действия токена %s истекло', $user->getToken()));
            });
        }

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
        $client->execute();

        $regionEntity = $user->getRegion();
        if ($regionEntity instanceof \Model\Region\Entity) {
            if (array_key_exists('reserve_as_buy', $regionConfig)) {
                $regionEntity->setForceDefaultBuy(false == $regionConfig['reserve_as_buy']);
            }
            $user->setRegion($regionEntity);
        }

        $region = $user->getRegion();

        // способы получения заказа
        $deliveryTypesById = [];
        foreach (\RepositoryManager::deliveryType()->getCollection() as $deliveryType) {
            $deliveryTypesById[$deliveryType->getId()] = $deliveryType;
        }

        // подготовка 2-го пакета запросов

        // TODO: запрашиваем меню

        // запрашиваем заказы пользователя
        /** @var $orders \Model\Order\Entity[] */
        $orders = [];
        \RepositoryManager::order()->prepareCollectionByUserToken($user->getToken(), function($data) use(&$orders) {
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
        $productsById = [];
        $servicesById = [];
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
        /**
         * @var $paymentGroups \Model\PaymentMethod\Group\Entity[]
         * @var $paymentMethodsById \Model\PaymentMethod\Entity[]
         */
        $paymentGroups = [];
        $paymentMethodsById = [];
        \RepositoryManager::paymentGroup()->prepareCollection($region,
            [
                'is_corporative' => $user->getEntity() ? $user->getEntity()->getIsCorporative() : false,
            ],
            function($data) use (
                &$paymentGroups,
                &$paymentMethodsById
            ) {
                if (!isset($data['detail'])) {
                    return;
                }

                foreach ($data['detail'] as $group) {
                    $paymentGroup = new \Model\PaymentMethod\Group\Entity($group);
                    if (!$paymentGroup->getPaymentMethods()) continue;

                    $paymentGroups[$paymentGroup->getId()] = $paymentGroup;

                    // заполняем отдельно массив $paymentMethodsById
                    foreach ($paymentGroup->getPaymentMethods() as $method) {
                        if (!$method instanceof \Model\PaymentMethod\Entity) continue;
                        $paymentMethodsById[$method->getId()] = $method;
                    }
                }
            }
        );

        // товары
        if ((bool)$productsById) {
            $chunksProductsById = array_chunk($productsById, \App::config()->coreV2['chunk_size'], true);

            foreach ($chunksProductsById as $i => $chunk) {
                \RepositoryManager::product()->prepareCollectionById(array_keys($chunk), $region, function($data) use(&$productsById) {
                    foreach($data as $item){
                        $productsById[$item['id']] = new \Model\Product\CartEntity($item);
                    }
                });
            }
        }

        // услуги
        if ((bool)$servicesById) {
            \RepositoryManager::service()->prepareCollectionById(array_keys($servicesById), $region, function($data) use(&$servicesById) {
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
        $page->setParam('deliveryTypesById', $deliveryTypesById);
        $page->setParam('paymentMethodsById', $paymentMethodsById);
        $page->setParam('paymentGroups', $paymentGroups);
        $page->setParam('orders', $orders);
        $page->setParam('productsById', $productsById);
        $page->setParam('servicesById', $servicesById);

        return new \Http\Response($page->show());
    }
}