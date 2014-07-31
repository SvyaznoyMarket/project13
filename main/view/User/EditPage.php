<?php

namespace View\User;

class EditPage extends \View\DefaultLayout {

    protected $layout  = 'layout-oneColumn';

    public function prepare() {
        // breadcrumbs
        if (!$this->hasParam('breadcrumbs')) {
            $breadcrumbs = [];
            $breadcrumbs[] = [
                'name' => 'Личный кабинет',
                'url'  => null,
            ];

            $this->setParam('breadcrumbs', $breadcrumbs);
        }

        $this->setTitle('Личный кабинет -> Профиль пользователя - Enter');
        $this->setParam('title', 'Личный кабинет');
    }

    public function slotContent() {
        return $this->render('user/page-edit', $this->params);
    }

    public function slotBodyDataAttribute() {
        return 'lk';
    }
}
