<?php

namespace View\Product;

class SlotPage extends \View\Product\IndexPage {
    public function slotContentHead() {
        return $this->render('product-slot/head', $this->params);
    }

    public function slotContent() {
        return $this->render('product-slot/content', $this->params) . PHP_EOL . $this->render('partner-counter/_flocktory_popup', $this->params);
    }
}