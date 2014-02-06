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

        $config->region->defaultId = $importedConfig->region['defaultId'];
        $config->region->cookieName = $importedConfig->region['cookieName'];

        $config->coreService->url = $importedConfig->coreV2['url'];
        $config->coreService->timeout = $importedConfig->coreV2['timeout'];
        $config->coreService->hugeTimeout = $importedConfig->coreV2['hugeTimeout'];
        $config->coreService->retryCount = $importedConfig->coreV2['retryCount'];
        $config->coreService->clientId = $importedConfig->coreV2['client_id'];

        $config->cmsService->url = $importedConfig->dataStore['url'];
        $config->cmsService->timeout = $importedConfig->dataStore['timeout'];
        $config->cmsService->retryCount = $importedConfig->dataStore['retryCount'];

        $config->adminService->enabled = $importedConfig->shopScript['enabled'];
        $config->adminService->url = $importedConfig->shopScript['url'];
        $config->adminService->timeout = $importedConfig->shopScript['timeout'];
        $config->adminService->retryCount = $importedConfig->shopScript['retryCount'];

        $config->reviewService->url = $importedConfig->reviewsStore['url'];
        $config->reviewService->timeout = $importedConfig->reviewsStore['timeout'];
        $config->reviewService->retryCount = $importedConfig->reviewsStore['retryCount'];

        $config->mustacheRenderer->dir = $config->dir . '/v2/vendor/mustache';
        $config->mustacheRenderer->templateDir = $config->dir . '/v2/template';
        $config->mustacheRenderer->cacheDir = (sys_get_temp_dir() ?: '/tmp') . '/mustache-cache';
        $config->mustacheRenderer->templateClassPrefix = preg_replace('/[^\w]/', '_', $config->hostname . '_v2' . '-');

        $config->product->itemPerPage = $importedConfig->product['itemsPerPage'];
        $config->productReview->enabled = $importedConfig->product['reviewEnabled'];
    }
}