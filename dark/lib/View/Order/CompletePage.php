<?php

namespace View\Order;

class CompletePage extends Layout {
    public function prepare() {
        $orders = is_array($this->getParam('orders')) ? $this->getParam('orders') : array();
        /** @var $order \Model\Order\Entity */
        $order = reset($orders);
        if ($order) {
            // если банк Ренессанс
            if (($order->getCredit() instanceof \Model\Order\Credit\Entity) && (\Model\CreditBank\Entity::PROVIDER_DIRECT_CREDIT == $order->getCredit()->getBankProviderId())) {
                $this->addStylesheet('http://direct-credit.ru/widget/style.css');
            }
        }

        $this->setTitle('Оформление заказа - Enter');
    }

    public function slotContent() {
        return $this->render('order/page-complete', $this->params);
    }

    public function slotBodyDataAttribute() {
        return 'order_complete';
    }

    public function slotYandexMetrika() {
        return (\App::config()->yandexMetrika['enabled']) ? $this->render('order/_yandexMetrika') : '';
    }
}
