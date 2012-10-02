<?php

namespace View\ProductCategory;

class LeafPage extends \View\DefaultLayout {
    public function slotContent() {
        return $this->render('product-category/page-leaf-index', $this->params);
    }
}
