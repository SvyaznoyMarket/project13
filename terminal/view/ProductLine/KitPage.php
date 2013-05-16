<?php

namespace Terminal\View\ProductLine;

class KitPage extends \Terminal\View\DefaultLayout {
    public function slotContent() {
        return $this->render('productLine/page-kit', $this->params);
    }
}
