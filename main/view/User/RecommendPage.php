<?php

namespace View\User;

use Helper\TemplateHelper;

class RecommendPage extends \View\DefaultLayout {
    /** @var string */
    protected $layout  = 'layout-oneColumn';

    public function prepare() {
        // breadcrumbs
        if (!$this->hasParam('breadcrumbs')) {
            $breadcrumbs = [];
            $breadcrumbs[] = array(
                'name' => 'Личный кабинет',
                'url'  => $this->url(\App::config()->user['defaultRoute']),
            );
        }

        $this->setTitle('Личный кабинет - Товары для вас - Enter');
        $this->setParam('title', 'Личный кабинет');
    }

    public function slotContent() {
        return $this->render('user/page-recommend', $this->params);
    }

    public function slotBodyDataAttribute() {
        return 'lk';
    }

    public function slotUserbar() {
        return $this->render('main/_userbar');
    }

    public function slotUserbarContentData() {
        return [
            'target' => '.js-showTopBar',
            'showWhenFullCartOnly' => true,
        ];
    }
}
