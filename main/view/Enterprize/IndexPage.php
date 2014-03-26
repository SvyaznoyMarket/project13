<?php

namespace View\Enterprize;

class IndexPage extends \View\DefaultLayout {
    protected $layout  = 'layout-oneColumn';

    public function prepare() {
        $this->setParam('title', '');
    }

    public function slotBodyDataAttribute() {
        return 'enterprize';
    }

    public function slotContent() {
        return $this->render('enterprize/page-index', $this->params);
    }

    public function slotBodyClassAttribute() {
        return 'enterprize';
    }

    public function slotContentHead() {
        return parent::slotContentHead() . $this->render('enterprize/_auth');
    }

    public function slotUserbarEnterprize() {
        return '';
    }

    public function slotUserbarEnterprizeContent() {
        return '';
    }
}
