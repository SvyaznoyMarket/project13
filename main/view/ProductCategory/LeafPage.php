<?php

namespace View\ProductCategory;

class LeafPage extends Layout {
    public function prepare() {
        if (\App::config()->productCategory['newShow']) {
            $this->layout = 'layout-oneColumn';
        }
    }

    public function slotContent() {
        $this->params['request'] = \App::request();

        return $this->render(\App::config()->productCategory['newShow'] ? 'product-category/page-leaf-new' : 'product-category/page-leaf', $this->params);
    }
}
