<?php

namespace View\Order;

class PaymentSuccessPage extends Layout {
    public function prepare() {
        $this->setTitle('Оформление заказа - Enter');
    }

    public function slotContent() {
        return $this->render('order/page-payment-success', $this->params);
    }

    public function slotBodyDataAttribute() {
        return 'order_complete';
    }

}
