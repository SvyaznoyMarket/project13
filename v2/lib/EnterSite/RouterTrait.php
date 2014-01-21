<?php

namespace EnterSite;

use Enter\Routing;

trait RouterTrait {
    /**
     * @return Routing\Router
     */
    public function getRouter() {
        if (!isset($GLOBALS[__METHOD__])) {
            $config = new Routing\Config();
            $GLOBALS[__METHOD__] = new Routing\Router($config);
        }

        return $GLOBALS[__METHOD__];
    }
}