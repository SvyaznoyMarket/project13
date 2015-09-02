<?php

namespace View\Content;


use View\LiteLayout;

class IndexPage extends LiteLayout
{
    protected $layout  = 'layout/content';

    public function blockContent() {
        return $this->getParam('htmlContent');
    }
}