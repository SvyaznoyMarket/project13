<?php

namespace View\Order;

class PaymentFailPage extends Layout {
    public function prepare() {
        $this->setTitle('Оформление заказа - Enter');
    }

    public function slotContent() {
        return $this->render('order/page-payment-fail', $this->params);
    }

    public function slotBodyDataAttribute() {
        return 'order_complete';
    }

}
