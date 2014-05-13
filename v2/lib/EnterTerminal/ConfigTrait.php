<?php

namespace EnterTerminal;

use EnterTerminal\Config;

trait ConfigTrait {
    /**
     * @return Config\Application
     */
    protected function getConfig() {
        $key = 'EnterSite\ConfigTrait::getConfig'; // FIXME

        if (!isset($GLOBALS[$key])) {
            $instance = new Config\Application();

            $GLOBALS[$key] = $instance;
        }

        return $GLOBALS[$key];
    }
}