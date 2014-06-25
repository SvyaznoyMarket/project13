<?php

namespace EnterSite\Routing\Region;

use EnterSite\Routing\Route;

class Autocomplete extends Route {
    public function __construct() {
        $this->action = ['Region\\Autocomplete', 'execute'];
        $this->parameters = [];
    }
}