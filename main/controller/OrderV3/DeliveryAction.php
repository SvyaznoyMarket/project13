<?php

namespace Controller\OrderV3;

class DeliveryAction {

    /**
     * @param \Http\Request $request
     * @return \Http\Response
     * @throws \Exception
     */
    public function execute(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $orderDelivery = new \Model\OrderDelivery\Entity(json_decode(file_get_contents(\App::config()->dataDir . '/data-store/cart-split.json'), true)['result']);
        //$orderDelivery = new \Model\OrderDelivery\Entity(\App::curl()->query('http://cms.enter.ru/mock/v2-cart-split.json'));
        if (!$orderDelivery) {
            throw new \Exception('Нет данных для разбиения заказа');
        }
        if (!(bool)$orderDelivery->orders) {
            throw new \Exception('Нет ни одного блока заказа');
        }

        $page = new \View\OrderV3\DeliveryPage();
        $page->setParam('orderDelivery', $orderDelivery);

        return new \Http\Response($page->show());
    }
}