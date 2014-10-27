<?php

namespace View\OrderV3;

class DeliveryPage extends Layout {
    public function prepare() {
        $this->setTitle('Оформление заказа - Enter');
    }

    public function slotContent() {
        $path = 'order-v3';
        if (\App::abTest()->getTest('orders_new') && \App::abTest()->getTest('orders_new')->getKey('orders_new_2')) {
            $path = 'order-v3-new';
        }
        return \App::closureTemplating()->render( $path . '/page-delivery', $this->params);
    }

    public function slotBodyDataAttribute() {
        return 'order-v3';
    }
}
