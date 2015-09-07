<?php

return function(\Config\AppConfig $c) {
    /** @var int $degradation */
    $degradation = isset($_SERVER['DEGRADATION_LEVEL']) ? (int)$_SERVER['DEGRADATION_LEVEL'] : 0;

    if (1 === $degradation) {
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

        $c->cart['productLimit'] = 20;

        $c->banner['checkStatus'] = false;
    }

};