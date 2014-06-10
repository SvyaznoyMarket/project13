<?php

namespace View\Photocontest;

class IndexPage extends \View\DefaultLayout {
    //protected $layout  = 'layout-main';
    protected $layout  = 'layout-oneColumn';

    protected function prepare() {
    }

    public function slotFooter() {
        return parent::slotFooter();
    }

    public function slotContentHead() {
        $photos = $this->getParam('photos');

        // заголовок контента страницы
        if (!$this->hasParam('title')) {
            $this->setParam('title', 'Фотоконкурс');
        }
        // навигация
        if (!$this->hasParam('breadcrumbs')) {
            $this->setParam('breadcrumbs', []);
        }

        return $this->render('photocontest/_contentHead', $this->params);
    }

    public function slotContent() {
        return $this->render('photocontest/page-index', $this->params);
    }
}
