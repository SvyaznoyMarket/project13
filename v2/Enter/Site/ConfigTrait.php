<?php

namespace Enter\Site;

trait ConfigTrait {
    /**
     * @return Config
     */
    public function getConfig() {
        if (!isset($GLOBALS[__METHOD__])) {
            $config = new Config();

            $config->region->defaultId = '14974';
            $config->region->cookieName = 'geoshop';

            $config->coreService->url = 'http://api.enter.ru/v2/';
            $config->coreService->timeout = 5;
            $config->coreService->retryCount = 2;

            $config->adminService->enabled = true;
            $config->adminService->url = 'http://admin.enter.ru/v2/';
            $config->adminService->timeout = 2;
            $config->adminService->retryCount = 2;

            $GLOBALS[__METHOD__] = $config;
        }

        return $GLOBALS[__METHOD__];
    }
}