<?php

namespace View\Product;

class PostBuyPage extends \View\Product\IndexPage {
    public function slotContentHead() {
        return $this->render('kitchen/product/head', $this->params);
    }

    public function slotContent() {
        return $this->render('kitchen/product/content', $this->params) . PHP_EOL . $this->render('partner-counter/_flocktory_popup', $this->params);
    }
}
