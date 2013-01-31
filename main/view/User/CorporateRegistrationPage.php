<?php

namespace View\User;

class CorporateRegistrationPage extends \View\DefaultLayout {
    protected $layout  = 'layout-oneColumn';

    public function prepare() {
        // breadcrumbs
        if (!$this->hasParam('breadcrumbs')) {
            $breadcrumbs = [];
            $breadcrumbs[] = array(
                'name' => 'Личный кабинет',
                'url'  => $this->url('user'),
            );
            $breadcrumbs[] = array(
                'name' => 'Регистрация юридического лица',
                'url'  => null,
            );

            $this->setParam('breadcrumbs', $breadcrumbs);
        }

        $this->setTitle('Регистрация юридического лица - Enter');
        $this->setParam('title', 'Регистрация юридического лица');
    }

    public function slotContent() {
        return $this->render('user/page-registerCorporate', $this->params);
    }

    public function slotBodyDataAttribute() {
        return 'infopage';
    }
}
