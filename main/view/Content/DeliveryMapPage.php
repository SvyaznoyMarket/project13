<?php

namespace View\Content;


class DeliveryMapPage extends \View\DefaultLayout {
//    protected $layout = 'layout-oneColumn';

    public function slotBodyDataAttribute() {
        return 'shop';
    }

    public function slotContent() {
        return $this->render('content/page-delivery', $this->params);
    }

    public function slotSidebar() {
        return $this->getParam('sidebar');
    }


}