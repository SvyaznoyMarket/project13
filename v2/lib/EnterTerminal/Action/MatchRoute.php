<?php

namespace EnterTerminal\Action;

use Enter\Http;
use EnterSite\LoggerTrait;
use EnterTerminal\Controller;

class MatchRoute {
    use LoggerTrait;

    /**
     * @param Http\Request $request
     * @return callable
     * @throws \Exception
     */
    public function execute(Http\Request $request) {
        $callable = null;

        try {
            $controllerClass = '\\EnterTerminal\\Controller\\' . implode('\\', array_map('ucfirst', explode('/', trim($request->getPathInfo(), '/')))); // TODO: перенести в настройки
            $this->getLogger()->push(['controller' => $controllerClass, 'action' => __METHOD__, 'tag' => ['routing']]);
            $callable = [new $controllerClass, 'execute'];
        } catch (\Exception $e) {
            $this->getLogger()->push(['type' => 'error', 'error' => $e, 'action' => __METHOD__, 'tag' => ['routing']]);

            //$callable = [new Controller\Error\NotFound(), 'execute'];
            throw $e;
        }

        return $callable;
    }
}