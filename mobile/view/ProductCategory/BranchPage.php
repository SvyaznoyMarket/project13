<?php

namespace Mobile\View\ProductCategory;

class BranchPage extends \Mobile\View\DefaultLayout {
    /**
     * @return string
     */
    public function slotContent() {
        return $this->render('product-category/page-branch', $this->params);
    }
}
