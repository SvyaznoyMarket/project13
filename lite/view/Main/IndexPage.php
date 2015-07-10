<?php

namespace View\Main;

use View\LiteLayout;

class IndexPage extends LiteLayout
{

    protected $layout = 'layout/main';

    protected function prepare()
    {
        parent::prepare();
        $this->addJavascript('/public/js/layouts/main.js');
    }


}