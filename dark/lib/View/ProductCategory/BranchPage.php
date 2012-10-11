<?php

namespace View\ProductCategory;

class BranchPage extends Layout {
    public function slotContent() {
        return $this->render('product-category/page-branch-index', $this->params);
    }
}
