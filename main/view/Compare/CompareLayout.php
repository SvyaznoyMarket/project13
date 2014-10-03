<?php


namespace View\Compare;


class CompareLayout extends \View\DefaultLayout {
    protected $layout = 'layout-compare';

    public function slotContent() {
        return \App::closureTemplating()->render('compare/page-index', $this->params);
    }

    public function slotBodyDataAttribute() {
        return 'compare';
    }
} 