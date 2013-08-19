<?php

namespace View\ProductCategory;

class BranchPage extends Layout {
    public function prepare() {
        if (\App::config()->productCategory['newShow']) {
            $this->layout = 'layout-oneColumn';
        }
    }

    public function slotContent() {
        return $this->render(\App::config()->productCategory['newShow'] ? 'product-category/page-branch-new' : 'product-category/page-branch', $this->params);
    }
}
