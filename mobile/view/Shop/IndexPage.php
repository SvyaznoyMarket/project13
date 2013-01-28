<?php

namespace Mobile\View\Shop;

class IndexPage extends \Mobile\View\DefaultLayout {
    /**
     * @return string
     */
    public function slotContent() {
        return $this->render('shop/page-index', $this->params);
    }
}