<?php

namespace View\Jewel\ProductCategory;

class BrandPage extends Layout {
    public function slotContent() {
        return $this->render('jewel/product-category/page-brand', $this->params);
    }
}
