<?php

namespace EnterSite\Routing\Order\Quick;

use Enter\Routing\Route;

class Index extends Route {
    public function __construct() {
        $this->action = ['Order\\Quick\\Index', 'execute'];
        $this->parameters = [];
    }
}