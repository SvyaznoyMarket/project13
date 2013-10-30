<?php

namespace Enter\Site;

trait ConfigTrait {
    public function getConfig() {
        static $config;

        if (!$config) {
            $config = new Config();
            $this->importFromV1($config);
        }

        return $config;
    }

    private function importFromV1(Config $config) {
        $v1Config = \App::config();

        $config->coreService->url = $v1Config->coreV2['url'];
        $config->coreService->timeout = $v1Config->coreV2['timeout'];
        $config->coreService->retryCount = $v1Config->coreV2['retryCount'];
    }
}