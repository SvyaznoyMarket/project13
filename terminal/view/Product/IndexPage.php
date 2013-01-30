<?php

namespace Terminal\View\Product;

class IndexPage extends \Terminal\View\DefaultLayout {
    public function slotContent() {
        return $this->render('product/page-index', $this->params);
    }
}
