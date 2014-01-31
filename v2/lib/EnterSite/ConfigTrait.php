<?php

namespace EnterSite;

use EnterSite\Config;

trait ConfigTrait {
    /**
     * @return Config\Application
     */
    public function getConfig() {
        if (!isset($GLOBALS[__METHOD__])) {
            $instance = new Config\Application();

            $instance->requestId = uniqid();
            $instance->dir = realpath(__DIR__ . '/../../..');
            $instance->hostname = 'enter.loc';

            $instance->region->defaultId = '14974';
            $instance->region->cookieName = 'geoshop';

            $instance->coreService->url = 'http://api.enter.ru/v2/';
            $instance->coreService->timeout = 5;
            $instance->coreService->retryCount = 2;

            $instance->cmsService->url = 'http://cms.enter.ru/v1/';
            $instance->cmsService->timeout = 1;
            $instance->cmsService->retryCount = 2;

            $instance->adminService->enabled = false;
            $instance->adminService->url = 'http://admin.enter.ru/v2/';
            $instance->adminService->timeout = 2;
            $instance->adminService->retryCount = 2;

            $instance->reviewService->url = 'http://admin.enter.ru/reviews/';
            $instance->reviewService->timeout = 2;
            $instance->reviewService->retryCount = 2;

            $instance->mustacheRenderer->dir = $instance->dir . '/v2/vendor/mustache';
            $instance->mustacheRenderer->templateDir = $instance->dir . '/v2/template';
            $instance->mustacheRenderer->cacheDir = (sys_get_temp_dir() ?: '/tmp') . '/mustache-cache';
            $instance->mustacheRenderer->templateClassPrefix = preg_replace('/[^\w]/', '_', $instance->hostname . '_v2' . '-');

            $instance->product->itemPerPage = 19;
            $instance->productReview->enabled = true;

            $GLOBALS[__METHOD__] = $instance;
        }

        return $GLOBALS[__METHOD__];
    }
}