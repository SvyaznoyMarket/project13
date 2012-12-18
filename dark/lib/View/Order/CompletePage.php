<?php

namespace View\Order;

class CompletePage extends Layout {
    public function prepare() {
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
