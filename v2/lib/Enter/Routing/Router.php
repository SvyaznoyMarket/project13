<?php

namespace Enter\Routing;

class Router {
    /** @var Config */
    protected $config;

    public function __construct(Config $config) {
        $this->config = $config;
    }

    public function getUrlByRoute(Route $route, array $parameters = []) {
        $routeClass = str_replace($this->config->routeClassPrefix, '', get_class($route));

        if (!isset($this->config->routes[$routeClass])) {
            throw new \RuntimeException(sprintf('Неизвестный маршрут %s', $routeClass));
        }

        $routeItem = $this->config->routes[$routeClass];
        $parameters = array_merge($parameters, $route->parameters);
        $vars = [];

        if (isset($parameters['#'])) {
            $anchor = '#' . $parameters['#'];
            unset($parameters['#']);
        } else {
            $anchor = '';
        }

        // если в шаблоне нет переменных ...
        if (false === strpos($routeItem['pattern'], '{')) {
            $url = $routeItem['pattern'];
            // ... иначе
        } else {
            $patternReplaces = [];
            preg_match_all('#\{(\w+)\}#', $routeItem['pattern'], $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);
            foreach ($matches as $match) {
                $varName = $match[1][0];
                if (!array_key_exists($varName, $parameters)) {
                    throw new \LogicException(sprintf('Не передан обязательный параметр %s для маршрута %s', $varName, $routeClass));
                }
                $patternReplaces['{' . $varName . '}'] = $parameters[$varName];
                $vars[$varName] = $parameters[$varName];
            }

            $url = strtr($routeItem['pattern'], $patternReplaces);
        }

        $extra = array_diff_key($parameters, $vars);
        if ((bool)$extra && $query = http_build_query($extra, '', '&')) {
            $url .= '?' . $query;
        }

        $url .= $anchor;

        // TODO: absolute

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