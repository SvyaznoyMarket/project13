<?php

namespace Terminal\View;

class DefaultLayout extends \Templating\HtmlLayout {
    protected $layout  = 'layout-default';

    public function __construct() {
        parent::__construct();
    }
}
