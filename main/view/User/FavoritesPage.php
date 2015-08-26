<?php

namespace View\User;

use Session\AbTest\ABHelperTrait;

class FavoritesPage extends \View\DefaultLayout {
    use ABHelperTrait;

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
        $this->setParam('title', 'Личный кабинет');
        $this->setParam('helper', new \Helper\TemplateHelper());
    }

    public function slotContent() {
        return $this->render(
            $this->isOldPrivate() ? 'user/page-favorites' : 'user/page-favorites-1508',
            $this->params
        );
    }

    public function slotBodyDataAttribute() {
        return 'lk';
    }
}