<?php

namespace View\Mobidengi;

class IndexPage extends \View\DefaultLayout {
    /** @var string */
    protected $layout  = 'layout-oneColumn';

    public function prepare() {

        // seo: title
        if (!$this->hasParam('title')) {
            $this->setTitle('МОБИ-ENTER – Enter.ru');
            $this->setParam('title', 'МОБИ-ENTER');
        }
    }

    public function slotBodyDataAttribute() {
        return 'infopage';
    }

    public function slotContent() {
        return $this->render('mobidengi/page-index', $this->params);
    }

}