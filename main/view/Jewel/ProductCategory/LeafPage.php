<?php

namespace View\Jewel\ProductCategory;

class LeafPage extends Layout {
    public function slotContent() {
        $this->params['request'] = \App::request();

        return $this->render('jewel/product-category/page-leaf', $this->params);
    }
}
