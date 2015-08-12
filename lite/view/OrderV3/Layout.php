<?php

namespace view\OrderV3;


class Layout extends \View\LiteLayout
{

    protected $layout = 'layout/order';

    public function blockOrderHead() {
        return $this->render('order/common/order-head', ['step' => 1]);
    }

}