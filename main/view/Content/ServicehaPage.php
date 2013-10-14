<?php

namespace View\Content;

class ServicehaPage extends \View\DefaultLayout {
    protected $layout  = 'layout-oneColumn';

    public function prepare() {
        // breadcrumbs
        if (!$this->hasParam('breadcrumbs')) {
            $breadcrumbs = array(
                array(
                    'name' => 'Услуги',
                    'url' => null,
                    ),
            );

            $this->setParam('breadcrumbs', $breadcrumbs);
        }
    }

    public function slotContent() {
        return $this->render('content/page-serviceha', $this->params);
    }

    public function slotBodyDataAttribute() {
        return 'infopage';
    }
}
