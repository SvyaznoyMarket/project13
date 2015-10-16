<?php

namespace View\Error;


use View\DefaultLayout;

class NotFoundPage extends DefaultLayout
{

    protected $layout = 'layout-clear';

    public function slotContent() {
        return $this->render('error/page-404');
    }

    public function slotBodyDataAttribute(){
        return 'page404';
    }
}