<?php

namespace EnterSite\Action;

use Enter\Http;
use EnterSite\ConfigTrait;
use EnterSite\LoggerTrait;
use EnterSite\DebugContainerTrait;
use EnterSite\RouterTrait;
use EnterSite\Controller;

class MatchRoute {
    use ConfigTrait, LoggerTrait, RouterTrait, DebugContainerTrait {
        ConfigTrait::getConfig insteadof LoggerTrait, RouterTrait, DebugContainerTrait;
        LoggerTrait::getLogger insteadof ConfigTrait;
    }

    /**
     * @param Http\Request $request
     * @return callable
     * @throws \Exception
     */
    public function execute(Http\Request $request) {
        $router = $this->getRouter();

        $callable = null;

        try {
            $route = $router->getRouteByPath($request->getPathInfo(), $request->getMethod(), $request->query->all());
            if ($this->getConfig()->debugLevel) $this->getDebugContainer()->route = [
                'name'       => get_class($route),
                'action'     => $route->action,
                'parameters' => $route->parameters,
            ];

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
        } catch (\Exception $e) {
            $this->getLogger()->push(['type' => 'error', 'error' => $e, 'action' => __METHOD__, 'tag' => ['routing']]);

            $callable = [new Controller\Error\NotFound(), 'execute'];
        }

        return $callable;
    }
}