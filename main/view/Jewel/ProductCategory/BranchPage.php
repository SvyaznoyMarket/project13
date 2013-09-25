<?php

namespace View\Jewel\ProductCategory;

class BranchPage extends Layout {
    public function slotContent() {
        return $this->render('jewel/product-category/page-branch', $this->params);
    }
}
