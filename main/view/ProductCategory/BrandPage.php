<?php

namespace View\ProductCategory;

class BrandPage extends Layout {
    public function slotContent() {
        return $this->render('product-category/page-brand', $this->params);
    }
}
