<?php

namespace View\ProductCategory;

class RootPage extends Layout {
    protected $layout  = 'layout-oneColumn';

    public function slotContent() {
        return $this->render('product-category/page-root-new', $this->params);
    }
}
