<?php

namespace View\ProductCategory;

class LeafPage extends Layout {
    public function slotContent() {
        return $this->render('product-category/page-leaf', $this->params);
    }
}
