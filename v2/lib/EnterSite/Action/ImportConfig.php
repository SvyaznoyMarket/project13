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
            $e = new \Exception('Не удалось импортировать настройки из ' . $configFile);
            die($e->getMessage());

            //throw $e;
        }

        $config->dir = $applicationDir;
        $config->hostname = $importedConfig->mainHost;

        $config->debugLevel = $importedConfig->debug ? 1 : 0;

        $config->logger->fileAppender->enabled = true;
        $config->logger->fileAppender->file = realpath($config->dir . '/../logs') . '/mobile.log';

        $config->session->name = $importedConfig->session['name'];
        $config->session->cookieLifetime = $importedConfig->session['cookie_lifetime'];
        $config->session->cookieDomain = $importedConfig->session['cookie_domain'];

        $config->userToken->authCookieName = $importedConfig->authToken['name'];

        $config->region->defaultId = (string)$importedConfig->region['defaultId'];
        $config->region->cookieName = $importedConfig->region['cookieName'];

        $config->credit->cookieName = 'credit_on';

        $config->curl->queryChunkSize = $importedConfig->coreV2['chunk_size'];
        $config->curl->logResponse = false;
        $config->curl->timeout = 90;
        $config->curl->retryTimeout = $importedConfig->coreV2['retryTimeout']['medium'];
        $config->curl->retryCount = $importedConfig->coreV2['retryCount'];

        $config->coreService->url = str_replace('/v2/', '/', $importedConfig->coreV2['url']);
        $config->coreService->timeout = $importedConfig->coreV2['timeout'];
        $config->coreService->hugeTimeout = $importedConfig->coreV2['hugeTimeout'];
        $config->coreService->clientId = $importedConfig->coreV2['client_id'];

        $config->cmsService->url = str_replace('/v1/', '/', $importedConfig->dataStore['url']);
        $config->cmsService->timeout = $importedConfig->dataStore['timeout'];

        $config->adminService->enabled = $importedConfig->shopScript['enabled'];
        $config->adminService->url = str_replace('/v2/', '/', $importedConfig->shopScript['url']);
        $config->adminService->timeout = $importedConfig->shopScript['timeout'];

        $config->reviewService->url = $importedConfig->reviewsStore['url'];
        $config->reviewService->timeout = $importedConfig->reviewsStore['timeout'];

        $config->contentService->url = $importedConfig->wordpress['url'];
        $config->contentService->timeout = $importedConfig->wordpress['timeout'];

        $config->retailRocketService->account = $importedConfig->partners['RetailRocket']['account'];
        $config->retailRocketService->url = $importedConfig->partners['RetailRocket']['apiUrl'];
        $config->retailRocketService->timeout = $importedConfig->partners['RetailRocket']['timeout'];

        $config->mediaHosts = $importedConfig->mediaHost;

        $config->product->itemPerPage = $importedConfig->product['itemsPerPage'];
        $config->product->itemsInSlider = $importedConfig->product['itemsInSlider'] * 2;
        $config->productReview->enabled = false; //$importedConfig->product['reviewEnabled']; // TODO: вернуть, когда починят
        $config->productPhoto->urlPaths = $importedConfig->productPhoto['url'];
        $config->productCategoryPhoto->urlPaths = $importedConfig->productCategory['url'];
        $config->search->minPhraseLength = $importedConfig->search['queryStringLimit'];
        $config->promo->urlPaths = $importedConfig->banner['url'];

        // собственные настройки
        $config->requestId = uniqid();

        $config->mustacheRenderer->dir = $config->dir . '/v2/vendor/mustache';
        $config->mustacheRenderer->templateDir = $config->dir . '/v2/template';
        $config->mustacheRenderer->cacheDir = (sys_get_temp_dir() ?: '/tmp') . '/mustache-cache';
        $config->mustacheRenderer->templateClassPrefix = preg_replace('/[^\w]/', '_', $config->hostname . '_v2' . '-');

        $config->product->itemsInSlider = 60;
        $config->productReview->itemsInCard = 7;

        $config->directCredit->enabled = (bool)$importedConfig->payment['creditEnabled'];
        $config->directCredit->minPrice = $importedConfig->product['minCreditPrice'];
        $config->directCredit->partnerId = '4427';
    }
}