<?php

namespace View\Friendship;

class IndexPage extends \View\DefaultLayout {
    protected $layout  = 'layout-oneColumn';

    public function prepare() {
        $this->setParam('title', 'ДРУЖИТЬ С НАМИ ВЫГОДНО И ИНТЕРЕСНО!');
    }

    public function slotBodyDataAttribute() {
        return 'infopage';
    }

    public function slotContent() {
        return $this->render('friendship/page-index');
    }

    public function slotBodyClassAttribute() {
        return parent::slotBodyClassAttribute() . ' friendship';
    }
}
