<?php

namespace View\OrderV3;

class ErrorPage extends Layout {
    public function prepare() {
        $this->setTitle('Оформление заказа');
    }

    public function blockContent() {
        return \App::closureTemplating()->render('order/_error.delivery', $this->params);
    }

    public function blockOrderHead() {
        return $this->render('order/common/order-head', ['step' => 1, 'hasErrors' => true]);
    }

    /** Для совместимости с контроллером
     * @return string
     */
    public function slotContent() {
        return $this->blockContent();
    }

    public function slotBodyDataAttribute() {
        return 'order-v3-new';
    }
}
