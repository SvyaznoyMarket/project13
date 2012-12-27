<?php

namespace View\User;

class ExternalLoginResponsePage extends \View\DefaultLayout {
    public function prepare() {
        $this->setParam('title', 'Авторизация');
    }

    public function slotContent() {
        return $this->render('user/external-login-response', $this->params);
    }
}
