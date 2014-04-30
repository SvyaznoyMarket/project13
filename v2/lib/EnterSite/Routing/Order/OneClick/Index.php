<?php

namespace EnterSite\Routing\Order\OneClick;

use Enter\Routing\Route;

class Index extends Route {
    public function __construct() {
        $this->action = ['Order\\OneClick\\Index', 'execute'];
        $this->parameters = [];
    }
}