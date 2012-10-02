<?php

namespace View\ProductCategory;

class BranchPage extends \View\DefaultLayout {
    public function slotContent() {
        return $this->render('product-category/page-branch-index', $this->params);
    }
}
