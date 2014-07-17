<?php

namespace View\OrderV3;

class CompletePage extends Layout {
    public function prepare() {
        $this->setTitle('Оформление заказа - Enter');
    }

    public function slotContent() {
        return \App::closureTemplating()->render('order-v3/page-complete', $this->params);
    }

    public function slotBodyDataAttribute() {
        //return 'order_new';
        return 'default';
    }
}
