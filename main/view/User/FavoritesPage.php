<?php

namespace View\User;


class FavoritesPage extends \View\DefaultLayout {

    /** @var string */
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

        $this->setTitle('Личный кабинет -> Избранное - Enter');
        $this->setParam('title', 'Избранное');
        $this->setParam('helper', new \Helper\TemplateHelper());
    }

    public function slotContent() {
        return $this->render('user/page-favorites', $this->params);
    }

    public function slotBodyDataAttribute() {
        return 'lk';
    }
}