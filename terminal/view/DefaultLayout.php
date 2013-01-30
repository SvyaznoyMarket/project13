<?php

namespace Terminal\View;

class DefaultLayout extends \Templating\HtmlLayout {
    protected $layout  = 'layout-default';

    public function __construct() {
        parent::__construct();

        $this->addJavascript('/js/jquery-1.6.4.min.js');
    }
}
