<?php

namespace EnterSite\Routing\Search;

use EnterSite\Routing\Route;

class Index extends Route {
    public function __construct($q) {
        $this->action = ['Search\\Index', 'execute'];
        $this->parameters = [
            'q' => $q,
        ];
    }
}