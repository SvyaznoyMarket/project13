<?php

namespace Terminal\View\ProductCategory;

class SortingPage extends \Terminal\View\DefaultLayout {
    public function slotContent() {
        return $this->render('productCategory/page-sorting', $this->params);
    }
}
