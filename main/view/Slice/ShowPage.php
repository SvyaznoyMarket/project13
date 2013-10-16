<?php

namespace View\Slice;

class ShowPage extends \View\DefaultLayout {
    protected $layout  = 'layout-oneColumn';

    public function slotContent() {
        return $this->render('slice/page-show', $this->params);
    }

    public function slotBodyDataAttribute() {
        return 'product_catalog';
    }
}
