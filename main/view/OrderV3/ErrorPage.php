<?php

namespace View\OrderV3;

class ErrorPage extends Layout {
    public function prepare() {
        $this->setTitle('Оформление заказа - Enter');
    }

    public function slotContent() {
        return \App::closureTemplating()->render('order-v3-new/page-error', $this->params);
    }

    public function slotBodyDataAttribute() {
        return 'order-v3-new';
    }
}
