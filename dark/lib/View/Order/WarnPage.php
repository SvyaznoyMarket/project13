<?php

namespace View\Order;

class WarnPage extends Layout {
    public function prepare() {
        $this->setTitle('Уточнение количества товаров в заказе - Enter');
    }

    public function slotContent() {
        return $this->render('order/page-warn', $this->params);
    }

    public function slotBodyDataAttribute() {
        return 'order_error';
    }
}
