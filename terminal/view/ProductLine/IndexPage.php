<?php

namespace Terminal\View\ProductLine;

class IndexPage extends \Terminal\View\DefaultLayout {
    public function slotContent() {
        return $this->render('productLine/page-index', $this->params);
    }
}
