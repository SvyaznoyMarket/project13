<?php

namespace Terminal\View\ProductCategory;

class FilterPage extends \Terminal\View\DefaultLayout {
    public function slotContent() {
        return $this->render('productCategory/page-filter', $this->params);
    }
}
