<?php

namespace Controller\Order;

class BillAction {
    public function __construct() {
        if (!\App::user()->getToken()) {
            throw new \Exception\AccessDeniedException();
        }
    }

    public function execute(\Http\Request $request, $orderNumber) {
        // TODO: доделать
        \App::logger()->debug('Exec ' . __METHOD__);

        $client = \App::coreClientV2();

        // TODO: в ядре еще не реализован метод api v2 получения заказа по номеру
        $order = \RepositoryManager::getOrder()->getEntityByNumber($orderNumber);
        if (!$order) {
            throw new \Exception\NotFoundException(sprintf('Заказ с номером "%s" не найден.', $orderNumber));
        }

        $page = new \View\Order\BillPage();

        return new \Http\Response($page->show());
    }
}