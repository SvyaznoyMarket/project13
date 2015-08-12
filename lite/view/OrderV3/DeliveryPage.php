<?php

namespace view\OrderV3;


class DeliveryPage extends \View\OrderV3\Layout
{
    public function blockContent() {
        return \App::closureTemplating()->render('order/page-delivery', $this->params + ['page' => $this]);
    }

    /** Для совместимости с контроллером
     * @return string
     */
    public function slotContent() {
        return $this->blockContent();
    }

    public function blockOrderHead() {
        return $this->render('order/common/order-head', ['step' => 2]);
    }

}