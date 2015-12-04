<?php

namespace View\User\Enterprize;

class IndexPage extends \View\DefaultLayout {

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

        $this->setTitle('Личный кабинет -> Enterprize - Enter');
        $this->setParam('title', 'Личный кабинет');
        $this->setParam('helper', new \Helper\TemplateHelper());
    }

    public function slotContent() {
        return $this->render('user/enterprize/page-index', $this->params);
    }

    public function slotBodyDataAttribute() {
        return 'lk';
    }
}