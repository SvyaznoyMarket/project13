<?php

namespace Enter\Routing;

class Router {
    /**
     * @param Route $route
     * @param array $params
     * @return string
     */
    public function getUrlByRoute(Route $route, array $params = []) {
        $url = $route->url . ((bool)$params ? ('?' . http_build_query($params)) : '');

        return $url;
    }

    /**
     * @param string $path
     * @param string|null $method
     * @return Route
     */
    public function getRouteByPath($path, $method = null) {
        return new Route();
    }
}