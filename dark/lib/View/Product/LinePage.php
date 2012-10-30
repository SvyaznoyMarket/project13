<?php

namespace View\Product;

class LinePage extends \View\DefaultLayout {
    protected $layout = 'layout-oneColumn';

    public function slotContent() {
        return $this->render('product/page-line', $this->params);
    }
}