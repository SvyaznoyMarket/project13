<?php

namespace View\Enterprize;

class CouponFailPage extends \View\DefaultLayout {
    protected $layout  = 'layout-oneColumn';

    public function prepare() {
        $this->setParam('title', '');
    }

    public function slotBodyDataAttribute() {
        return 'enterprize';
    }

    public function slotContent() {
        return $this->render('enterprize/page-fail', $this->params);
    }

    public function slotBodyClassAttribute() {
        return 'enterprize_user';
    }
}
