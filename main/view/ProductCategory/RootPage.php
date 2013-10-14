<?php

namespace View\ProductCategory;

class RootPage extends Layout {
    public function prepare() {
        parent::prepare();

        if (\App::config()->product['newList']) {
            $this->layout = 'layout-oneColumn';
        }
    }

    public function slotContent() {
        return $this->render(\App::config()->product['newList'] ? 'product-category/page-root-new' : 'product-category/page-root', $this->params);
    }
}
