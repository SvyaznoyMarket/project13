<?php

namespace EnterSite\Routing\Region;

use Enter\Routing\Route;

class Set extends Route {
    /**
     * @param string $regionId
     */
    public function __construct($regionId) {
        $this->action = ['Region\\Set', 'execute'];
        $this->parameters = [
            'regionId' => $regionId,
        ];
    }
}