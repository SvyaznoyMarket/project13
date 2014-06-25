<?php

namespace EnterSite\Routing\Region;

use EnterSite\Routing\Route;

class SetByName extends Route {
    public function __construct() {
        $this->action = ['Region\\Set', 'execute'];
        $this->parameters = [];
    }
}