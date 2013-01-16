<?php

namespace View\User;

class ChangePasswordPage extends \View\DefaultLayout {
    public function prepare() {
        // breadcrumbs
        if (!$this->hasParam('breadcrumbs')) {
            $breadcrumbs = array();
            $breadcrumbs[] = array(
                'name' => 'Личный кабинет',
                'url'  => $this->url('user'),
            );
            $breadcrumbs[] = array(
                'name' => 'Изменение пароля',
                'url'  => null,
            );

            $this->setParam('breadcrumbs', $breadcrumbs);
        }

        $this->setTitle('Изменение пароля - Enter');
        $this->setParam('title', 'Изменение пароля');
    }

    public function slotContent() {
        return $this->render('user/page-changePassword', $this->params);
    }

    public function slotSidebar() {
        return $this->render('user/_sidebar', $this->params);
    }

    public function slotBodyDataAttribute() {
        return 'infopage';
    }
}
