<?php

require_once __DIR__ . '/AppConfig.php';

class DevConfig extends \AppConfig {
    protected function initialize() {
        parent::initialize();

        $this->env = 'dev';
        $this->debug = true;

        $this->coreV2['url'] = 'http://olga.core.ent3.ru/v2/';
        $this->coreV2['client_id'] = 'site';
    }
}