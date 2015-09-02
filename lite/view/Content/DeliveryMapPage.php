<?php

namespace View\Content;

class DeliveryMapPage extends IndexPage {
    protected $layout  = 'layout/content';

    public function blockContent() {
        return $this->render('content/delivery', $this->params);
    }
}