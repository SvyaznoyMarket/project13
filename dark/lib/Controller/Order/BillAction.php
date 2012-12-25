<?php

namespace Controller\Order;

class BillAction {
    public function __construct() {
        if (!\App::user()->getEntity()) {
            throw new \Exception\AccessDeniedException();
        }
    }

    public function execute(\Http\Request $request, $orderNumber) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $client = \App::curl();

        $userEntity = \App::user()->getEntity();

        $order = \RepositoryManager::getOrder()->getEntityByNumberAndPhone($orderNumber, $userEntity->getMobilePhone());
        if (!$order) {
            throw new \Exception\NotFoundException(sprintf('Заказ с номером "%s" не найден.', $orderNumber));
        }

        try {
            $content = $client->query(\App::config()->coreV2['url'] . 'order-bill/get', array(
                'client_id' => \App::config()->coreV2['client_id'],
                'token'     => $userEntity->getToken(),
                'order_id'  => $order->getId(),
            ));
            if (!empty($content)) {
                return new \Http\Response($content);
            }
        } catch (\Exception $e) {
            \App::logger()->error($e);
        }

        $page = new \View\Order\BillPage();
        $page->setParam('order', $order);

        return new \Http\Response($page->show());
    }
}