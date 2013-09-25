<?php

namespace View\Jewel\ProductCategory;

class RootPage extends Layout {
    public function slotContent() {
        return $this->render('jewel/product-category/page-root', $this->params);
    }
}
