<?php

namespace View\Content;

class IndexPage extends \View\DefaultLayout {
    protected $layout  = 'layout-oneColumn';

    public function prepare() {
        // breadcrumbs
        if (!$this->hasParam('breadcrumbs')) {
            $breadcrumbs = array(
                array(
                    'name' => 'Помощь пользователю',
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
