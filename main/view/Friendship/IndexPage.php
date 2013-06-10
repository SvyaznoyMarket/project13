<?php

namespace View\Friendship;

class IndexPage extends \View\DefaultLayout {
    protected $layout  = 'layout-oneColumn';

    public function prepare() {
        $this->setParam('title', 'Дружить с нами интересно!');
    }

    public function slotBodyDataAttribute() {
        return 'infopage';
    }

    public function slotContent() {
        return $this->render('friendship/page-index');
    }

    public function slotBodyClassAttribute() {
        return 'friendship';
    }
}
