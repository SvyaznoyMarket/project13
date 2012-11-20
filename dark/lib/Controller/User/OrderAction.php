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

        // способы получения заказа
        $deliveryTypesById = array();
        foreach (\RepositoryManager::getDeliveryType()->getCollection() as $deliveryType) {
            $deliveryTypesById[$deliveryType->getId()] = $deliveryType;
        }

        // заказы
        $orders = \RepositoryManager::getOrder()->getCollectionByUserToken(\App::user()->getToken());

        // сортировка по дате desc
        /** @var $orders \Model\Order\Entity[] */
        $orders = array_reverse($orders);

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

        $paymentMethodsById = array();
        $client->addQuery('payment-method/get', array(
            'geo_id' => \RepositoryManager::getRegion()->getDefaultEntity()->getId(),
        ), array(), function($data) use(&$paymentMethodsById) {
            foreach($data as $item){
                $paymentMethodsById[$item['id']] = new \Model\PaymentMethod\Entity($item);
            }
        });

        if ((bool)$productsById) {
            $client->addQuery('product/get', array(
                'select_type' => 'id',
                'id'          => array_keys($productsById),
                'geo_id'      => \App::user()->getRegion()->getId(),
            ), array(), function($data) use(&$productsById) {
                foreach($data as $item){
                    $productsById[$item['id']] = new \Model\Product\CartEntity($item);
                }
            });
        }

        if ((bool)$servicesById) {
            $client->addQuery('service/get2', array(
                'id'     => array_keys($servicesById),
                'geo_id' => \App::user()->getRegion()->getId(),
            ), array(), function($data) use(&$servicesById) {
                foreach($data as $item){
                    $servicesById[$item['id']] = new \Model\Product\Service\Entity($item);
                }
            });
        }

        if ((bool)$productsById || (bool)$servicesById) {
            $client->execute();
        }

        $page = new \View\User\OrderPage();
        $page->setParam('deliveryTypesById', $deliveryTypesById);
        $page->setParam('paymentMethodsById', $paymentMethodsById);
        $page->setParam('orders', $orders);
        $page->setParam('productsById', $productsById);
        $page->setParam('servicesById', $servicesById);

        return new \Http\Response($page->show());
    }
}