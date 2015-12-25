<?php

namespace View\Enterprize;

class FishkaPage extends \View\DefaultLayout {
    protected $layout  = 'layout-oneColumn';

    public function prepare() {
        $this->setParam('title', '');
    }

    public function slotBodyDataAttribute() {
        return 'enterprize';
    }

    public function slotContent() {
        return $this->render('enterprize/page-fishka', $this->params);
    }

    public function slotBodyClassAttribute() {
        return parent::slotBodyClassAttribute() . ' enterprize_user';
    }
}