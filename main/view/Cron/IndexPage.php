<?php

namespace View\Cron;

class IndexPage extends \View\DefaultLayout {
    protected $layout  = 'layout-oneColumn';

    public function prepare() {
        // breadcrumbs
        if (!$this->hasParam('breadcrumbs')) {
            $breadcrumbs = array(
                array(
                    'name' => 'Задания планировщика',
                    'url' => null,
                    ),
            );


            $this->setParam('breadcrumbs', $breadcrumbs);
        }

        $this->setTitle('Задания планировщика');
    }

    public function slotContent() {
        return $this->render('cron/page-index', $this->params);
    }

    public function slotBodyDataAttribute() {
        return 'infopage';
    }
}
