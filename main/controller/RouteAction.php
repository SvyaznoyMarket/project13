<?php

namespace Controller;

class RouteAction {
    public function execute(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        if (!$request->isXmlHttpRequest()) {
            throw new \Exception\NotFoundException('Request is not xml http request');
        }

        $router = \App::router();

        $actions = (array)$request->get('request');
        if (!(bool)$actions) {
            throw new \Exception('Не перадан обязательный параметр request');
        }

        foreach ($actions as $action) {
            $action = array_merge([
                'url'  => null,
                'data' => [],
            ], (array)$action);
            if (!is_array($action['data'])) {
                $action['data'] = [];
            }
        }

        return new \Http\Response();
    }
}
