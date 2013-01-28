<?php

namespace Mobile\View\ProductCategory;

class IndexPage extends \Mobile\View\DefaultLayout {
    /**
     * @return string
     */
    public function slotContent() {
        return $this->render('product-category/page-index', $this->params);
    }
}
