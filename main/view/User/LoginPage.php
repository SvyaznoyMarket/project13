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
        return 'infopage';
    }
    
    public function slotFooter() {
        $client = \App::contentClient();

        try {
            $response = $client->query('footer_compact');
        } catch (\Exception $e) {
            \App::exception()->add($e);
            \App::logger()->error($e);

            $response = array('content' => '');
        }

        return $response['content'];
    }
}
