<?php

require_once __DIR__ . '/DevConfig.php';

class LocalConfig extends \DevConfig {
    protected function initialize() {
        parent::initialize();

        $this->env = 'local';
        $this->debug = true;

        $this->coreV2['url'] = 'http://enter-core.loc/v2/';

        $this->product['globalListEnabled'] = false;
    }
}