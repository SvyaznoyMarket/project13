<?php

namespace view\OrderV3;


class Layout extends \View\LiteLayout
{

    protected $layout = 'layout/order';

    protected function prepare()
    {
        parent::prepare();
    }

    public function blockContent() {
        return $this->render('order/page-new');
    }


}