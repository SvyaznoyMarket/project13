<?php

namespace View\Error;


use View\LiteLayout;

class NotFoundPage extends LiteLayout
{

    protected $layout = 'layout/clear';

    public function blockContent() {
        return $this->render('error/page-404');
    }

}