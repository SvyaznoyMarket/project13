<?php

namespace Routing;

use Http\Request;

class ActionResolver {
    private $controllerPrefix = 'Controller';

    /**
     * @param string $controllerPrefix
     */
    public function __construct($controllerPrefix = null) {
        if ($controllerPrefix) {
            $this->controllerPrefix = $controllerPrefix;
        }
    }

    /**
     * @param \Http\Request $request
     * @return array
     * @throws \RuntimeException
     * @throws \Exception\NotFoundException
     */
    public function getCall(Request $request) {
        if (!is_array($request->attributes->get('action'))) {
            throw new \Exception\NotFoundException('Запрос не содержит действия');
        }

        list ($actionName, $actionMethod) = $request->attributes->get('action');

        $r = new \ReflectionClass($this->controllerPrefix . '\\' . $actionName);
        $action = $r->newInstanceArgs();

        $attributes = $request->attributes->all();
        $arguments = array();
        $r = new \ReflectionMethod($action, $actionMethod);
        foreach ($r->getParameters() as $param) {
            if (array_key_exists($param->name, $attributes)) {
                $arguments[] = $attributes[$param->name];
            } elseif ($param->getClass() && $param->getClass()->isInstance($request)) {
                $arguments[] = $request;
            }
            elseif ($param->isDefaultValueAvailable()) {
                $arguments[] = $param->getDefaultValue();
            }
            else {
                throw new \RuntimeException(sprintf('Действию "%s" необходим обязательный параметр "%s"', get_class($action), $param->name));
            }
        }

        return array(array($action, $actionMethod), $arguments);
    }
}