<?php

require_once __DIR__ . '/AppConfig.php';

class ProdConfig extends \AppConfig {
    protected function initialize() {
        parent::initialize();

        $this->env = 'prod';
        $this->debug = false;

        $this->coreV2['url'] = 'http://api.enter.ru/v2/';
        $this->coreV2['client_id'] = 'site';

        $this->googleAnalytics['enabled'] = true;
        $this->yandexMetrika['enabled'] = true;
    }
}