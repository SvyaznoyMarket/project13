<?php

namespace View\Product;

class SlotPage extends \View\Product\IndexPage {
    public function slotContentHead() {
        return '';
    }

    public function slotContent() {
        return $this->render('product-slot/content', $this->params);
    }
}
