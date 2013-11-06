<?php

namespace View\ProductCategory;

class BranchPage extends Layout {
    protected $layout  = 'layout-oneColumn';

    public function slotContent() {
        return $this->render('product-category/page-branch', $this->params);
    }
}
