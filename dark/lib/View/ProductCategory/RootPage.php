<?php

namespace View\ProductCategory;

class RootPage extends \View\DefaultLayout {
    public function slotContent() {
        return $this->render('product-category/page-root-index', $this->params);
    }
}
