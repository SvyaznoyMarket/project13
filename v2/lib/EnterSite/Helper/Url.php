<?php

namespace EnterSite\Helper;

use Enter\Http;
use Enter\Routing;

class Url {
    /**
     * @param Routing\Route $route
     * @param Http\Request $request
     * @param array $replaces
     * @return array
     */
    public function replace(Routing\Route $route, Http\Request $request, array $replaces) {
        $parameters = [];

        foreach ($request->query as $name => $value) {
            if (array_key_exists($name, $route->parameters)) continue;

            $parameters[$name] = $value;
        }

        foreach ($replaces as $name => $value) {
            if ((null === $value) && array_key_exists($name, $parameters)) {
                unset($parameters[$name]);
                continue;
            }

            $parameters[$name] = $value;
        }

        return $parameters;
    }
}