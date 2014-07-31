<?php

namespace View\User;

use Helper\TemplateHelper;

class OrderPage extends \View\DefaultLayout {
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

        $this->setTitle('Личный кабинет - Заказ - Enter');
        $this->setParam('title', 'Личный кабинет');
    }

    public function slotContent() {
        return $this->render('user/page-order', $this->params);
    }

    public function slotBodyDataAttribute() {
        return 'lk';
    }
}
