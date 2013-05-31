<?php

namespace View\Friendship;

class IndexPage extends \View\DefaultLayout {
    protected $layout  = 'layout-oneColumn';

    public function prepare() {
        $this->setParam('title', 'Дружить с нами интересно!');
    }

    public function slotContent() {
        return $this->render('friendship/page-index');
    }
}
