<?php

namespace View\Cron;

class IndexPage extends \View\DefaultLayout {
    protected $layout  = 'layout-oneColumn';

    public function prepare() {
        // breadcrumbs
        if (!$this->hasParam('breadcrumbs')) {
            $breadcrumbs = array(
                array(
                    'name' => 'Задание планировщика',
                    'url' => null,
                    ),
            );


            $this->setParam('breadcrumbs', $breadcrumbs);
        }
    }

    public function slotContent() {
        return $this->getParam('content');

    }

    public function slotBodyDataAttribute() {
        return 'infopage';
    }
}
