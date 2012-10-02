<?php

namespace View\Product;

class IndexPage extends \View\DefaultLayout {
    public function slotContent() {
        return $this->render('product/page-index', $this->params);
    }
}
