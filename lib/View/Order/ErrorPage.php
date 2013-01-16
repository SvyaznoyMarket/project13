<?php

namespace View\Order;

class ErrorPage extends Layout {
    public function prepare() {
        $this->setTitle('Ошибка при оформление заказа - Enter');
    }

    public function slotContent() {
        return $this->render('order/page-error', $this->params);
    }

    public function slotBodyDataAttribute() {
        return 'order';
    }
}
