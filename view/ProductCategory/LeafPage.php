<?php

namespace View\ProductCategory;

class LeafPage extends Layout {
    public function slotContent() {
        $this->params['request'] = \App::request();

        return $this->render('product-category/page-leaf', $this->params);
    }
}
