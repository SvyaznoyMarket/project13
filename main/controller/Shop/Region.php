<?php

namespace Controller\Shop;

class Region {
    /**
     * @param int $regionId
     * @return \Http\RedirectResponse
     */
    public function execute($regionId) {
        return new \Http\RedirectResponse(\App::router()->generate('shop'), 301);
    }
}