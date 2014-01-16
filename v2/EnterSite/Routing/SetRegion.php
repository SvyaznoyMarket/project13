<?php

namespace EnterSite\Routing;

use Enter\Routing\Route;
use EnterSite\Model\Region;

class SetRegion extends Route {
    /**
     * @param Region $region
     */
    public function __construct(Region $region) {
        $this->action = 'Page\\Region\\SetObjectByHttpRequest';
        $this->url = '/region/set/' . $region->id;
    }
}