<?php

require_once __DIR__ . '/AppConfig.php';

class DevConfig extends \AppConfig {
    protected function initialize() {
        parent::initialize();

        $this->env = 'dev';
        $this->debug = true;

        //$this->coreV2['url'] = 'http://api.enter.ru/v2/';
        //$this->coreV2['url'] = 'http://core.ent3.ru/v2/';
        $this->coreV2['url'] = 'http://test2.core.ent3.ru/v2/';
        $this->coreV2['client_id'] = 'site';

        $this->coreV1 = array(
            //'url'          => 'http://api.enter.ru/v1/json',
            'url'          => 'http://core.ent3.ru/v1/json',
            'client_id'    => 'site',
            //'consumer_key' => 's7urupe7Efrus8un',
            //'signature'    => 'mEbradRUBruprUT7ukuvAxupraCrEcuk',
            'consumer_key' => 'test',
            'signature'    => 'test',
        );

        $this->smartEngine = array(
            'pull' => true,
            'push' => false,
        );

        $this->warranty = array(
            'enabled' => true,
        );

        $this->product['globalListEnabled'] = true;
    }
}