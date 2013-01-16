<?php

namespace View\Order;

class CreatePage extends Layout {
    public function prepare() {
        $this->setTitle('Оформление заказа - Enter');
    }

    public function slotContent() {
        return $this->render('order/page-create', $this->params);
    }

    public function slotBodyDataAttribute() {
        return 'order';
    }

    public function slotYandexMetrika() {
        return (\App::config()->yandexMetrika['enabled']) ? $this->render('order/_yandexMetrika') : '';
    }
}
