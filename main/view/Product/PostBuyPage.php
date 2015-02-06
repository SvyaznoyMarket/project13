<?php

namespace View\Product;

class PostBuyPage extends \View\Product\IndexPage {
    public function slotContentHead() {
        return $this->render('postBuy/product/head', $this->params);
    }

    public function slotContent() {
        return $this->render('postBuy/product/content', $this->params) . PHP_EOL . $this->render('partner-counter/_flocktory_popup', $this->params);
    }
}
