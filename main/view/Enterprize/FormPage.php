<?php

namespace View\Enterprize;

class FormPage extends \View\DefaultLayout {
    protected $layout  = 'layout-oneColumn';

    public function prepare() {
        $this->setParam('title', '');
    }

    public function slotBodyDataAttribute() {
        return 'enterprize';
    }

    public function slotContent() {
        if ($this->params['limit'] > 0) {
            return $this->render('enterprize/page-form', $this->params);
        }

        return '';
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
