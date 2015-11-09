<?php

namespace View\User;

class LoginPage extends \View\DefaultLayout {
    protected $layout = 'layout-login';

    public function prepare() {
        $this->setTitle('Авторизация - Enter');
        $this->setParam('title', 'Авторизация');
    }

    public function slotContent() {
        return $this->render('user/page-login', $this->params);
    }

    public function slotContentHead() {
        $this->setParam('hasSearch', false);

        return parent::slotContentHead();
    }

    public function slotBodyDataAttribute() {
        return 'login';
    }
}
