<?php

namespace view\Compare;


use View\LiteLayout;

class CompareLayout extends LiteLayout
{

    protected $layout = 'layout/compare';

    public function blockContent() {
        return \App::closureTemplating()->render('compare/content', $this->params);
    }

}