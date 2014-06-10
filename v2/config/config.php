<?php

return function(\EnterSite\Config\Application $config) {
    $config->requestId = uniqid();

    $config->dir = realpath(__DIR__ . '/../..');
    $config->hostname = 'enter.ru';

    $config->debug = false;

    $config->logger->fileAppender->file = realpath($config->dir . '/../logs') . '/mobile.log';

    $config->session->name = 'enter';
    $config->session->cookieLifetime = 15552000;
    $config->session->cookieDomain = '.enter.ru';

    $config->userToken->authCookieName = '_token';

    $config->region->defaultId = '14974';
    $config->region->cookieName = 'geoshop';

    $config->credit->cookieName = 'credit_on';

    $config->curl->queryChunkSize = 50;
    $config->curl->logResponse = false;
    $config->curl->timeout = 90;
    $config->curl->retryTimeout = 0.5;
    $config->curl->retryCount = 2;

    $config->coreService->url = 'http://api.enter.ru/';
    $config->coreService->timeout = 5;
    $config->coreService->clientId = 'site';

    $config->cmsService->url = 'http://cms.enter.ru/';
    $config->cmsService->timeout = 1;

    $config->adminService->enabled = true;
    $config->adminService->url = 'http://admin.enter.ru/';
    $config->adminService->timeout = 2;

    $config->reviewService->url = 'http://admin.enter.ru/reviews/';
    $config->reviewService->timeout = 2;

    $config->contentService->url = 'http://content.enter.ru/';

    $config->retailRocketService->account = '519c7f3c0d422d0fe0ee9775';
    $config->retailRocketService->url = 'http://api.retailrocket.ru/api/';
    $config->retailRocketService->timeout = 0.5;

    $config->mustacheRenderer->dir = $config->dir . '/v2/vendor/mustache';
    $config->mustacheRenderer->templateDir = $config->dir . '/v2/template';
    $config->mustacheRenderer->cacheDir = (sys_get_temp_dir() ?: '/tmp') . '/mustache-cache';
    $config->mustacheRenderer->templateClassPrefix = preg_replace('/[^\w]/', '_', $config->hostname . '_v2' . '-');

    $config->mediaHosts = [
        0 => 'http://fs01.enter.ru',
        1 => 'http://fs02.enter.ru',
        2 => 'http://fs03.enter.ru',
        3 => 'http://fs04.enter.ru',
        4 => 'http://fs05.enter.ru',
        5 => 'http://fs06.enter.ru',
        6 => 'http://fs07.enter.ru',
        7 => 'http://fs08.enter.ru',
        8 => 'http://fs09.enter.ru',
        9 => 'http://fs10.enter.ru',
    ];

    $config->product->itemPerPage = 19;
    $config->product->itemsInSlider = 60;
    $config->productReview->enabled = true;
    $config->productReview->itemsInCard = 7;
    $config->productPhoto->urlPaths = [
        0 => '/1/1/60/',
        1 => '/1/1/120/',
        2 => '/1/1/163/',
        3 => '/1/1/500/',
        4 => '/1/1/2500/',
        5 => '/1/1/1500/',
    ];
    $config->productCategoryPhoto->urlPaths = [
        0 => '/6/1/163/',
        3 => '/6/1/500/',
    ];

    $config->search->minPhraseLength = 1;

    $config->promo->urlPaths =[
        0 => '/4/1/230x302/',
        1 => '/4/1/768x302/',
        2 => '/4/1/920x320/',
    ];

    $config->directCredit->enabled = true;
    $config->directCredit->minPrice = 3000;
    $config->directCredit->partnerId = '4427';
};