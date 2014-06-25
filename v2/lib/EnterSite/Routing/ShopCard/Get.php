<?php

namespace EnterSite\Routing\ShopCard;

use EnterSite\Routing\Route;

class Get extends Route {
    /**
     * @param string $shopToken
     * @param string $regionToken
     */
    public function __construct($shopToken, $regionToken) {
        $this->action = ['ShopCard', 'execute'];
        $this->parameters = [
            'regionToken' => $regionToken,
            'shopToken'   => $shopToken,
        ];
    }
}