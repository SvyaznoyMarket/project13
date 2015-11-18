<?php

return function(\Config\AppConfig $c, \Http\Request $request = null) {
    /** @var int $degradation */
    $c->degradation = $request ? (int)$request->headers->get('X-Enter-Degradation-Level') : 0;
    //$c->degradation = isset($_SERVER['DEGRADATION_LEVEL']) ? (int)$_SERVER['DEGRADATION_LEVEL'] : 0;

    // отключение некритичного функционала, повторных запросов
    if ($c->degradation > 0) {
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
        $c->cart['productLimit'] = 7;

        $c->banner['checkStatus'] = false;

        $c->abTest['enabled'] = false;

        $c->subscribe['getChannel'] = false;
    }

    // отключение функционала
    if ($c->degradation > 1) {
        $c->product['reviewEnabled'] = false;
        $c->product['couponEnabledInCard'] = false;
        $c->product['viewedEnabled'] = false;
        $c->mainMenu['recommendationsEnabled'] = false;
        $c->product['getModel'] = false;
        $c->product['smartChoiceEnabled'] = false;
        $c->product['pushRecommendation'] = false;
        $c->product['creditEnabledInCard'] = false;
    }

    // отключение расчета доставки, корзины в Москве (только одноклик)
    if ($c->degradation > 2) {
        $c->product['deliveryCalc'] = false;
        $c->cart['oneClickOnly'] = true;
    }

    // агрессивное кеширование, отключение связанных товаров
    if ($c->degradation > 3) {
        $c->region['cache'] = true;
        if (!$c->debug) {
            $c->logger['emptyChance'] = 67;
        }

        $c->product['pullRecommendation'] = false;
        $c->mainMenu['maxLevel'] = 2;
    }

    // отключение редиректа
    if ($c->degradation > 4) {
        $c->redirect301['enabled'] = false;
        $c->product['breadcrumbsEnabled'] = false;
        $c->mainMenu['maxLevel'] = 1;
    }
};