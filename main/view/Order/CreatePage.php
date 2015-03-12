<?php

namespace View\Order;

/**
 * Class CreatePage
 * @package View\Order
 * @deprecated
 */
class CreatePage extends Layout {
    public function prepare() {
        $this->setTitle('Оформление заказа - Enter');
    }

    public function slotContent() {
        if (\App::abTest()->getTest('other') && \App::abTest()->getTest('other')->getChosenCase()->getKey() == 'emails') {
            return $this->render('order/page-create-abtest-email', $this->params);
        }
        return $this->render('order/page-create', $this->params);
    }

    public function slotBodyDataAttribute() {
        return 'order';
    }
}
