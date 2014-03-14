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

    public function slotContentHead() {
        // заголовок контента страницы
        if (!$this->hasParam('title')) {
            $this->setParam('title', null);
        }
        // навигация
        if (!$this->hasParam('breadcrumbs')) {
            $this->setParam('breadcrumbs', []);
        }

        return $this->render('enterprize/_contentHead', $this->params);
    }

    public function slotUserbarEnterprize() {
        return '';
    }

    public function slotUserbarEnterprizeContent() {
        return '';
    }
}
