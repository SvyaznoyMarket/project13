<?php

namespace View\Enterprize;

class CouponCompletePage extends \View\DefaultLayout {
    protected $layout  = 'layout-oneColumn';

    public function prepare() {
        $this->setParam('title', '');
    }

    public function slotBodyDataAttribute() {
        return 'enterprize';
    }

    public function slotContent() {
        return $this->render('enterprize/page-complete', $this->params);
    }

    public function slotBodyClassAttribute() {
        return 'enterprize_user';
    }
}
