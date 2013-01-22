<?php

namespace View;

class Layout extends \Templating\HtmlLayout {
    public function __construct() {
        parent::__construct();

        $this->helper = new Helper();
    }
}