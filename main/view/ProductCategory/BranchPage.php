<?php

namespace View\ProductCategory;

class BranchPage extends Layout {
    public function prepare() {
        if (\App::config()->product['newList']) {
            $this->layout = 'layout-oneColumn';
        }
    }

    public function slotContent() {
        return $this->render(\App::config()->product['newList'] ? 'product-category/page-branch-new' : 'product-category/page-branch', $this->params);
    }
}
