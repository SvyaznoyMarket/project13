<?php

namespace View\Enterprize;

class IndexPage extends \View\DefaultLayout {
    protected $layout  = 'layout-oneColumn';

    public function prepare() {
        $this->setParam('title', '');
    }

    public function slotBodyDataAttribute() {
        return 'infopage';
    }

    public function slotContent() {
        return $this->render('enterprize/page-index');
    }

    public function slotBodyClassAttribute() {
        return 'enterprize';
    }
}
