<?php

return function(\EnterTerminal\Config\Application $config) {
    $config->requestId = uniqid();

    $config->dir = realpath(__DIR__ . '/../../..');
    $config->hostname = 't.enter.ru';

    $config->logger->fileAppender->file = $config->dir . '/log/terminal.log';

    $config->session->name = 'clientId';
    $config->session->cookieLifetime = 15552000;
    $config->session->cookieDomain = null;

    $config->userToken->authCookieName = null; // TODO: убрать из настроек терминала

    $config->region->defaultId = '14974';
    $config->region->cookieName = null; // TODO: убрать из настроек терминала

    $config->coreService->url = 'http://api.enter.ru/';
    $config->coreService->timeout = 5;
    $config->coreService->retryCount = 2;
    $config->coreService->clientId = 'terminal'; // переопределяется из http.request

    $config->cmsService->url = 'http://cms.enter.ru/';
    $config->cmsService->timeout = 1;
    $config->cmsService->retryCount = 2;

    $config->adminService->enabled = true;
    $config->adminService->url = 'http://admin.enter.ru/';
    $config->adminService->timeout = 2;
    $config->adminService->retryCount = 2;

    $config->reviewService->url = 'http://admin.enter.ru/reviews/';
    $config->reviewService->timeout = 2;
    $config->reviewService->retryCount = 2;

    $config->retailRocketService->account = '519c7f3c0d422d0fe0ee9775';
    $config->retailRocketService->url = 'http://api.retailrocket.ru/api/';
    $config->retailRocketService->timeout = 0.5;

    $config->curl->queryChunkSize = 50;

    // TODO: убрать из настроек терминала
    //$config->mustacheRenderer;

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
    $config->productPhoto->urlPaths = [
        0 => '/1/1/60/',
        1 => '/1/1/120/',
        2 => '/1/1/163/',
        3 => '/1/1/500/',
        4 => '/1/1/2500/',
        5 => '/1/1/1500/',
    ];
    $config->productReview->enabled = true;
    $config->productReview->itemsInCard = 7;
};