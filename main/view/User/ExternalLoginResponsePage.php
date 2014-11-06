<?php

namespace View\User;

class ExternalLoginResponsePage extends \View\DefaultLayout {
    protected $layout  = 'layout-oneColumn';
    
    public function prepare() {
        $this->setParam('title', 'Авторизация');
    }

    public function slotContent() {
        return $this->render('user/page-external-login-response', $this->params);
    }
}
