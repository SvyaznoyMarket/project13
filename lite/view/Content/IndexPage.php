<?php

namespace View\Content;


use View\LiteLayout;

class IndexPage extends LiteLayout
{
    protected $layout  = 'layout/content.withMenu';

    protected function prepare() {
        if ($this->getParam('token') === 'pickpoint-help') {
            $this->layout = 'layout/content.withoutMenu';
        }

        return parent::prepare();
    }

    public function blockContent() {
        return $this->getParam('htmlContent');
    }

}