<?php

namespace EnterSite;

use EnterSite\Config;

trait ConfigTrait {
    /**
     * @return Config\Application
     */
    protected function getConfig() {
        if (!isset($GLOBALS[__METHOD__])) {
            $instance = new Config\Application();

            $GLOBALS[__METHOD__] = $instance;
        }

        return $GLOBALS[__METHOD__];
    }
}