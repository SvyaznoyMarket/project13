<?php

namespace EnterSite\Action;

use Enter\Http;
use EnterSite\RouterTrait;

class MatchRoute {
    use RouterTrait;

    /**
     * @param Http\Request $request
     * @return callable
     * @throws \Exception
     */
    public function execute(Http\Request $request) {
        $router = $this->getRouter();

        $callable = null;

        $route = $router->getRouteByPath($request->getPathInfo(), $request->getMethod());
        if (isset($route->action[0])) {
            $controllerClass = '\\EnterSite\\Controller\\' . $route->action[0]; // TODO: перенести в настройки
            $callable = [new $controllerClass, $route->action[1]];
        }
        if (!$callable || !is_callable($callable)) {
            throw new \Exception(sprintf('Маршруту %s не задан обработчик', get_class($route)));
        }

        // замена GET-параметров route-параметрами
        foreach ($route->parameters as $key => $value) {
            $request->query[$key] = $value;
        }

        return $callable;
    }
}