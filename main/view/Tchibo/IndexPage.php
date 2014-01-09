<?php

namespace View\Tchibo;

class IndexPage extends \View\DefaultLayout {
    protected $layout = 'layout-oneColumn';

    public function slotBodyDataAttribute() {
        return 'product_catalog';
    }

    public function slotContent() {
        return $this->render('tchibo/page-index', $this->params);
    }
}