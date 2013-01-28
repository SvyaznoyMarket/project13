<?php

namespace Mobile\View\Main;

class IndexPage extends \Mobile\View\DefaultLayout {
    protected $layout  = 'layout-default';

    public function slotContent() {
        return $this->render('main/page-index', $this->params);
    }
}
