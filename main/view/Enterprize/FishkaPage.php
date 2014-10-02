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
        return 'enterprize_user';
    }

    public function slotContentHead() {
        return parent::slotContentHead() . $this->render('enterprize/_auth');
    }
} 