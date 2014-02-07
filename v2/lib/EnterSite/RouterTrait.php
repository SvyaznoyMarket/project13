<?php

namespace EnterSite;

use Enter\Routing;

trait RouterTrait {
    /**
     * @return Routing\Router
     */
    protected function getRouter() {
        if (!isset($GLOBALS[__METHOD__])) {
            $config = new Routing\Config();
            $config->routeClassPrefix = 'EnterSite\Routing\\';
            $config->routes = json_decode(file_get_contents(__DIR__ . '/../../config/route.json'), true); // TODO брать из Config\Application\Router
            $GLOBALS[__METHOD__] = new Routing\Router($config);
        }

        return $GLOBALS[__METHOD__];
    }
}