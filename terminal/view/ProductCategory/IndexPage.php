<?php

namespace Terminal\View\ProductCategory;

class IndexPage extends \Terminal\View\DefaultLayout {
    public function slotContent() {
        return $this->render('productCategory/page-index', $this->params);
    }
}
