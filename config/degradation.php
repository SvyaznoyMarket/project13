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
        $c->product['recommendationProductLimit'] = 7;
        $c->cart['productLimit'] = 7;
        $c->banner['checkStatus'] = false;
        $c->abTest['enabled'] = false;
        $c->subscribe['getChannel'] = false;

        if (!$c->debug) {
            $c->logger['emptyChance'] = 30;
        }
    }

    // отключение функционала
    if ($c->degradation > 1) {
        $c->region['cache'] = true;
        $c->product['reviewEnabled'] = false;
        $c->product['viewedEnabled'] = false;
        $c->mainMenu['recommendationsEnabled'] = false;
        $c->product['getModelInListing'] = false;
        $c->product['smartChoiceEnabled'] = false;
        $c->product['pushRecommendation'] = false;
        $c->product['creditEnabledInCard'] = false;

        if (!$c->debug) {
            $c->logger['emptyChance'] = 50;
        }
    }

    // отключение расчета доставки, корзины в Москве (только одноклик)
    if ($c->degradation > 2) {
        $c->product['pullMainRecommendation'] = false;
        $c->mainMenu['maxLevel'] = 2;
        $c->eventService['enabled'] = false;
        $c->product['deliveryCalc'] = false;

        if (!$c->debug) {
            $c->logger['emptyChance'] = 70;
        }
    }

    // агрессивное кеширование, отключение связанных товаров
    if ($c->degradation > 3) {
        $c->mainMenu['maxLevel'] = 1;
        $c->product['pullRecommendation'] = false;

        if (!$c->debug) {
            $c->logger['emptyChance'] = 90;
        }
    }
    if ($c->degradation === 4) {
        $c->useNodeMQ = true;
    }

    // отключение редиректа
    if ($c->degradation > 4) {
        $c->cart['oneClickOnly'] = true;
        $c->redirect301['enabled'] = false;
        $c->product['getModelInCard'] = false;
        $c->product['breadcrumbsEnabled'] = false;

        if (!$c->debug) {
            $c->logger['emptyChance'] = 95;
        }
    }
};