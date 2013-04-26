<?php

namespace Terminal\View\ProductCategory;

class ModelPage extends \Terminal\View\DefaultLayout {
    public function slotContent() {
        return $this->render('productCategory/page-model', $this->params);
    }
}
