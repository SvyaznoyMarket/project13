<?php

namespace View\User;

class EditPage extends \View\DefaultLayout {
    public function prepare() {
        // breadcrumbs
        if (!$this->hasParam('breadcrumbs')) {
            $breadcrumbs = [];
            $breadcrumbs[] = array(
                'name' => 'Личный кабинет',
                'url'  => $this->url('user'),
            );
            $breadcrumbs[] = array(
                'name' => 'Профиль пользователя',
                'url'  => null,
            );

            $this->setParam('breadcrumbs', $breadcrumbs);
        }

        $this->setTitle('Профиль пользователя - Enter');
        $this->setParam('title', 'Профиль пользователя');
    }

    public function slotContent() {
        return $this->render('user/page-edit', $this->params);
    }

    public function slotSidebar() {
        return $this->render('user/_sidebar', $this->params);
    }

    public function slotBodyDataAttribute() {
        return 'infopage';
    }
}
