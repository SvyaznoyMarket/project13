<?php

namespace EnterSite\Routing;

use Enter\Routing\Route;
use EnterSite\Model\Region;

class SetRegion extends Route {
    /**
     * @param Region $region
     */
    public function __construct(Region $region) {
        $this->action = 'Region\\Set';
        $this->url = '/region/set/' . $region->id;
    }
}