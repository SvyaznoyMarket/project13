<?php

namespace EnterSite;

use Enter\Routing\Router;
use Enter\Routing\Config;

trait RouterTrait {
    /**
     * @return Router
     */
    public function getRouter() {
        if (!isset($GLOBALS[__METHOD__])) {
            $config = new Config();
            $GLOBALS[__METHOD__] = new Router($config);
        }

        return $GLOBALS[__METHOD__];
    }
}