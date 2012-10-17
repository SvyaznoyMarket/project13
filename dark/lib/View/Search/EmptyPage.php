<?php

namespace View\Search;

class EmptyPage extends \View\DefaultLayout {
    protected $layout = 'layout-oneColumn';

    public function prepare() {
        if (!$this->hasParam('title')) {
            $this->setParam('title', 'Вы искали “' . $this->getParam('searchQuery') . '”');
        }
    }

    public function slotContent() {
        return $this->render('search/page-empty', $this->params);
    }
}
