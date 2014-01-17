<?php

namespace EnterSite;

use EnterSite\Config\Application as ApplicationConfig;

trait ConfigTrait {
    /**
     * @return ApplicationConfig
     */
    public function getConfig() {
        if (!isset($GLOBALS[__METHOD__])) {
            $instance = new ApplicationConfig();

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

            $instance->adminService->enabled = true;
            $instance->adminService->url = 'http://admin.enter.ru/v2/';
            $instance->adminService->timeout = 2;
            $instance->adminService->retryCount = 2;

            $instance->mustacheRenderer->dir = $instance->dir . '/vendor/mustache';
            $instance->mustacheRenderer->templateDir = $instance->dir . '/v2/template';
            $instance->mustacheRenderer->cacheDir = (sys_get_temp_dir() ?: '/tmp') . '/mustache-cache';
            $instance->mustacheRenderer->templateClassPrefix = preg_replace('/[^\w]/', '_', $instance->hostname . '_v2' . '-');

            $instance->productList->itemPerPage = 19;

            $GLOBALS[__METHOD__] = $instance;
        }

        return $GLOBALS[__METHOD__];
    }
}