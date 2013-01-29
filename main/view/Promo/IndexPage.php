<?php

namespace View\Promo;

class IndexPage extends \View\DefaultLayout {
    protected $layout  = 'layout-oneColumn';

    public function slotContent() {
        return $this->render('promo/page-index', $this->params);
    }
}
