<?php

namespace EnterSite;

use EnterSite\Config\Application as ApplicationConfig;

trait ConfigTrait {
    /**
     * @return ApplicationConfig
     */
    public function getConfig() {
        if (!isset($GLOBALS[__METHOD__])) {
            $config = new ApplicationConfig();

            $config->region->defaultId = '14974';
            $config->region->cookieName = 'geoshop';

            $config->coreService->url = 'http://api.enter.ru/v2/';
            $config->coreService->timeout = 5;
            $config->coreService->retryCount = 2;

            $config->cmsService->url = 'http://cms.enter.ru/v1/';
            $config->cmsService->timeout = 1;
            $config->cmsService->retryCount = 2;

            $config->adminService->enabled = true;
            $config->adminService->url = 'http://admin.enter.ru/v2/';
            $config->adminService->timeout = 2;
            $config->adminService->retryCount = 2;

            $config->productList->itemPerPage = 19;

            $GLOBALS[__METHOD__] = $config;
        }

        return $GLOBALS[__METHOD__];
    }
}