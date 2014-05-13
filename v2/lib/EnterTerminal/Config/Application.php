<?php

namespace EnterTerminal\Config {
    use EnterSite\Config\Application as BaseApplicationConfig;

    class Application extends BaseApplicationConfig {
        /** @var string */
        public $clientId;

        public function __construct() {
            parent::__construct();
        }
    }
}
