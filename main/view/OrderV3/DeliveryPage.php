<?php

namespace View\OrderV3;

class DeliveryPage extends Layout {
    public function prepare() {
        $this->setTitle('Оформление заказа - Enter');
    }

    public function slotContent() {
        //return \App::closureTemplating()->render('order-v3/page-delivery', $this->params);
        return \App::closureTemplating()->render('order-v3/page', $this->params);
    }

    public function slotBodyDataAttribute() {
        //return 'order_new';
        return 'order-v3';
    }
}
