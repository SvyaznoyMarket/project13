<?php

namespace View\Enterprize;

class EmptyPage extends \View\DefaultLayout {
    protected $layout  = 'layout-oneColumn';

    public function prepare() {
        $this->setParam('title', '');
    }

    public function slotBodyDataAttribute() {
        return 'infopage';
    }

    public function slotContent() {
        return $this->render('enterprize/page-empty', $this->params);
    }

    public function slotBodyClassAttribute() {
        return parent::slotBodyClassAttribute() . ' enterprize_user';
    }
}
