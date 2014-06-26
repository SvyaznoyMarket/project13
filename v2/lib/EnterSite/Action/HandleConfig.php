<?php

namespace EnterSite\Action;

use EnterSite\ConfigTrait;

class HandleConfig {
    use ConfigTrait;

    /**
     * @param string $environment
     * @param bool $debugLevel
     */
    public function execute($environment, $debugLevel) {
        $config = $this->getConfig();

        $config->environment = $environment;
        $config->debugLevel = $debugLevel;
        if ($config->debugLevel) {
            $config->logger->fileAppender->file = str_replace('.log', '-debug.log', $config->logger->fileAppender->file);
        }
        if (2 == $config->debugLevel) {
            $config->curl->logResponse = true;
        }
    }
}