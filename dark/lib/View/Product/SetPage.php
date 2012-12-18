<?php

namespace View\Product;

class SetPage extends \View\DefaultLayout {
    /** @var string */
    protected $layout  = 'layout-twoColumn';

    public function prepare() {
    }

    public function slotContent() {
        return $this->render('product/page-set', $this->params);
    }

    public function slotBodyDataAttribute() {
        return 'product_catalog';
    }

    public function slotSidebar() {
        return $this->render('product/page-set-sidebar', $this->params);
    }
}
