<?php

namespace EnterSite\Action;

use Enter\Util\JsonDecoderTrait;
use EnterSite\ConfigTrait;

class LoadCachedConfig {
    use JsonDecoderTrait;
    use ConfigTrait;

    public function execute($configFile) {
        // cache
        $GLOBALS['EnterSite\ConfigTrait::getConfig'] = $this->jsonToObject(file_get_contents($configFile));

        $config = $this->getConfig();

        $config->requestId = uniqid();
    }
}