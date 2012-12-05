<?php
namespace Controller\Smartengine;

class Action {

    public function pushBuy($orderNumber) {
        // TODO: доделать
        \App::logger()->debug('Exec ' . __METHOD__);

        // TODO: в ядре еще не реализован метод api v2 получения заказа по номеру
        $order = \RepositoryManager::getOrder()->getEntityByNumber($orderNumber);
        if (!$order) {
            throw new \Exception\NotFoundException(sprintf('Заказ с номером "%s" не найден.', $orderNumber));
        }

    }
}