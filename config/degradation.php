<?php

return function(\Config\AppConfig $c) {
    /** @var int $degradation */
    $degradation = isset($_SERVER['DEGRADATION_LEVEL']) ? (int)$_SERVER['DEGRADATION_LEVEL'] : 0;

    if (1 === $degradation) {
        $c->reviewsStore['retryCount'] = 1;
        $c->pickpoint['retryCount'] = 1;

        $c->partners['RetailRocket']['timeout'] = 0.2;

        $c->product['showAccessories'] = false;
        $c->product['pullRecommendation'] = false;
        $c->product['pushRecommendation'] = false;
        $c->product['viewedEnabled'] = false;
        $c->product['showRelated'] = false;
        $c->product['reviewEnabled'] = false;

        $c->cart['productLimit'] = 20;
    }

};