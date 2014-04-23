<?php

namespace EnterSite\Routing\Region;

use Enter\Routing\Route;

class Autocomplete extends Route {
    public function __construct() {
        $this->action = ['Region\\Autocomplete', 'execute'];
        $this->parameters = [];
    }
}