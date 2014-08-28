<?php

namespace View\User;

class ExternalLoginResponsePage extends \View\DefaultLayout {
    public function prepare() {
        $this->setParam('title', 'Авторизация');
    }

    public function slotContent() {
        return $this->render('user/page-external-login-response', $this->params);
    }
}
