<?php

namespace view\OrderV3;


class DeliveryPage extends \View\OrderV3\Layout
{
    public function blockContent() {
        return \App::closureTemplating()->render('order/page-delivery', $this->params + ['page' => $this]);
    }

}