<?php

namespace Terminal\View\ProductLine;

class PartPage extends \Terminal\View\DefaultLayout {
    public function slotContent() {
        return $this->render('productLine/page-part', $this->params);
    }
}
