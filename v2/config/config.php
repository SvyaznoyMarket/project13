<?php

return function(\EnterSite\Config\Application $config) {
    $config->requestId = uniqid();

    $config->dir = realpath(__DIR__ . '/../..');
    $config->hostname = 'enter.loc';

    $config->logger->fileAppender->file = $config->dir . '/log/main.log';

    $config->region->defaultId = '14974';
    $config->region->cookieName = 'geoshop';

    $config->coreService->url = 'http://api.enter.ru/v2/';
    $config->coreService->timeout = 5;
    $config->coreService->retryCount = 2;
    $config->coreService->clientId = 'site';

    $config->cmsService->url = 'http://cms.enter.ru/v1/';
    $config->cmsService->timeout = 1;
    $config->cmsService->retryCount = 2;

    $config->adminService->enabled = true;
    $config->adminService->url = 'http://admin.enter.ru/v2/';
    $config->adminService->timeout = 2;
    $config->adminService->retryCount = 2;

    $config->reviewService->url = 'http://admin.enter.ru/reviews/';
    $config->reviewService->timeout = 2;
    $config->reviewService->retryCount = 2;

    $config->mustacheRenderer->dir = $config->dir . '/v2/vendor/mustache';
    $config->mustacheRenderer->templateDir = $config->dir . '/v2/template';
    $config->mustacheRenderer->cacheDir = (sys_get_temp_dir() ?: '/tmp') . '/mustache-cache';
    $config->mustacheRenderer->templateClassPrefix = preg_replace('/[^\w]/', '_', $config->hostname . '_v2' . '-');

    $config->product->itemPerPage = 19;
    $config->productReview->enabled = true;
    $config->productReview->itemsInCard = 7;
};