<?php

namespace View\User\Address;

class IndexPage extends \View\DefaultLayout {

    /** @var string */
    protected $layout  = 'layout-oneColumn';

    public function prepare() {
        // breadcrumbs
        if (!$this->hasParam('breadcrumbs')) {
            $breadcrumbs = [];
            $breadcrumbs[] = [
                'name' => 'Личный кабинет',
                'url'  => $this->url(\App::config()->user['defaultRoute']),
            ];

            $this->setParam('breadcrumbs', $breadcrumbs);
        }

        $this->setTitle('Личный кабинет -> Адреса - Enter');
        $this->setParam('title', 'Личный кабинет');
        $this->setParam('helper', new \Helper\TemplateHelper());
    }

    public function slotContent() {
        return $this->render('user/address/page-index', $this->params);
    }

    public function slotBodyDataAttribute() {
        return 'lk';
    }
}