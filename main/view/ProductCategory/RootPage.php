<?php

namespace View\ProductCategory;

class RootPage extends Layout {
    public function prepare() {
        if (\App::config()->productCategory['newShow']) {
            $this->layout = 'layout-oneColumn';
        }
    }

    public function slotContent() {
        return $this->render(\App::config()->productCategory['newShow'] ? 'product-category/page-root-new' : 'product-category/page-root', $this->params);
    }
}
