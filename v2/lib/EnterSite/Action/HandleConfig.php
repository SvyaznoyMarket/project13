<?php

namespace EnterSite\Action;

use EnterSite\ConfigTrait;

class HandleConfig {
    use ConfigTrait;

    /**
     * @param string $environment
     * @param bool $debug
     */
    public function execute($environment, $debug) {
        $config = $this->getConfig();

        $config->environment = $environment;
        $config->debug = $debug;
    }
}