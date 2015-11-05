<?php

return function(\Config\AppConfig $c, \Http\Request $request = null) {
    /** @var int $degradation */
    $c->degradation = $request ? (int)$request->headers->get('X-Enter-Degradation-Level') : 0;
    //$c->degradation = isset($_SERVER['DEGRADATION_LEVEL']) ? (int)$_SERVER['DEGRADATION_LEVEL'] : 0;

    if (1 === $c->degradation) {
        $c->coreV2['retryCount'] = 1;
        $c->corePrivate['retryCount'] = 1;
        $c->searchClient['retryCount'] = 1;
        $c->reviewsStore['retryCount'] = 1;
        $c->dataStore['retryCount'] = 1;
        $c->scms['retryCount'] = 1;
        $c->scmsV2['retryCount'] = 1;
        $c->scmsSeo['retryCount'] = 1;
        $c->crm['retryCount'] = 1;
        $c->pickpoint['retryCount'] = 1;

        $c->product['recommendationProductLimit'] = 7;
        $c->product['creditEnabledInCard'] = false;
        $c->cart['productLimit'] = 7;

        $c->banner['checkStatus'] = false;

        $c->abTest['enabled'] = false;
    }

};