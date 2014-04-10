<?php

namespace EnterSite\Action;

use EnterSite\ConfigTrait;

class ImportConfig {
    use ConfigTrait;

    public function execute($applicationDir, $configFile) {
        $config = $this->getConfig();

        require_once $applicationDir . '/lib/Config/AppConfig.php';
        $importedConfig = include $configFile;

        if (!$importedConfig instanceof \Config\AppConfig) {
            throw new \Exception('не удалось импортировать настройки');
        }

        $config->dir = $applicationDir;
        $config->hostname = $importedConfig->mainHost;

        $config->logger->fileAppender->file = $config->dir . '/log/main.log';

        $config->session->name = $importedConfig->session['name'];
        $config->session->cookieLifetime = $importedConfig->session['cookie_lifetime'];

        $config->userToken->authCookieName = $importedConfig->authToken['name'];
        $config->userToken->infoCookieName = $importedConfig->authToken['authorized_cookie'];

        $config->region->defaultId = (string)$importedConfig->region['defaultId'];
        $config->region->cookieName = $importedConfig->region['cookieName'];

        $config->curl->queryChunkSize = $importedConfig->coreV2['chunk_size'];

        $config->coreService->url = str_replace('/v2/', '/', $importedConfig->coreV2['url']);
        $config->coreService->timeout = $importedConfig->coreV2['timeout'];
        $config->coreService->hugeTimeout = $importedConfig->coreV2['hugeTimeout'];
        $config->coreService->retryCount = $importedConfig->coreV2['retryCount'];
        $config->coreService->clientId = $importedConfig->coreV2['client_id'];

        $config->cmsService->url = str_replace('/v1/', '/', $importedConfig->dataStore['url']);
        $config->cmsService->timeout = $importedConfig->dataStore['timeout'];
        $config->cmsService->retryCount = $importedConfig->dataStore['retryCount'];

        $config->adminService->enabled = $importedConfig->shopScript['enabled'];
        $config->adminService->url = str_replace('/v2/', '/', $importedConfig->shopScript['url']);
        $config->adminService->timeout = $importedConfig->shopScript['timeout'];
        $config->adminService->retryCount = $importedConfig->shopScript['retryCount'];

        $config->reviewService->url = $importedConfig->reviewsStore['url'];
        $config->reviewService->timeout = $importedConfig->reviewsStore['timeout'];
        $config->reviewService->retryCount = $importedConfig->reviewsStore['retryCount'];

        $config->retailRocketService->account = $importedConfig->partners['RetailRocket']['account'];
        $config->retailRocketService->url = $importedConfig->partners['RetailRocket']['apiUrl'];
        $config->retailRocketService->timeout = $importedConfig->partners['RetailRocket']['timeout'];

        $config->mediaHosts = $importedConfig->mediaHost;

        $config->product->itemPerPage = $importedConfig->product['itemsPerPage'];
        $config->product->itemsInSlider = $importedConfig->product['itemsInSlider'] * 2;
        $config->productPhoto->urlPaths = $importedConfig->productPhoto['url'];
        $config->productReview->enabled = $importedConfig->product['reviewEnabled'];

        // собственные настройки
        $config->requestId = uniqid();

        $config->mustacheRenderer->dir = $config->dir . '/v2/vendor/mustache';
        $config->mustacheRenderer->templateDir = $config->dir . '/v2/template';
        $config->mustacheRenderer->cacheDir = (sys_get_temp_dir() ?: '/tmp') . '/mustache-cache';
        $config->mustacheRenderer->templateClassPrefix = preg_replace('/[^\w]/', '_', $config->hostname . '_v2' . '-');

        $config->product->itemsInSlider = 60;
        $config->productReview->itemsInCard = 7;
    }
}