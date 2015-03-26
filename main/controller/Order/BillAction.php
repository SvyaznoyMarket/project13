<?php

namespace Controller\Order;

class BillAction {
    public function __construct() {
        if (!\App::user()->getEntity()) {
            throw new \Exception\AccessDeniedException();
        }
    }

    public function execute(\Http\Request $request, $orderNumber) {
        //\App::logger()->debug('Exec ' . __METHOD__, ['order']);

        $client = \App::curl();

        $userEntity = \App::user()->getEntity();

        $order = \RepositoryManager::order()->getEntityByNumberAndPhone($orderNumber, $userEntity->getMobilePhone());
        if (!$order) {
            throw new \Exception\NotFoundException(sprintf('Заказ с номером "%s" не найден.', $orderNumber));
        }

        try {
            $content = [];
            $content = $client->addQuery(\App::config()->coreV2['url'] . 'order-bill/get?' . http_build_query([
                'client_id' => \App::config()->coreV2['client_id'],
                'token'     => $userEntity->getToken(),
                'order_id'  => $order->getId(),
            ]), [], function ($data) use (&$content) {
                $bill = reset($data);
                $content = base64_decode($bill['bill']);
            }, \App::config()->coreV2['hugeTimeout']);
            $client->execute(\App::config()->coreV2['retryTimeout']['default'], \App::config()->coreV2['retryCount']);

            if (!empty($content)) {
                return new \Http\Response($content);
            }
        } catch (\Exception $e) {
            \App::logger()->error($e, ['order']);
        }

        $page = new \View\Order\BillPage();
        $page->setParam('order', $order);

        return new \Http\Response($page->show());
    }
}