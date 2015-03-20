<?php

namespace View\Product;

class GifteryPage extends \View\Product\IndexPage {
    public function slotContentHead() {
        return $this->render('product-giftery/head', $this->params);
    }

    public function slotContent() {
        return $this->render('product-giftery/content', $this->params);
    }
}
