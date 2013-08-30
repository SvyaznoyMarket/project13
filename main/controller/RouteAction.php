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

        $responseData = [];
        foreach ($actions as $action) {
            $action = array_merge([
                'url'    => null,
                'method' => 'GET',
                'data'   => [],
            ], (array)$action);
            if (!is_array($action['data'])) {
                $action['data'] = [];
            }

            try {
                $router->match($action['url'], $action['method']);
            } catch (\Exception $e) {
                $responseData[$action['url']] = [
                    'success' => false,
                    'error'   => ['code' => $e->getCode(), 'message' => $e->getMessage()],
                ];
            }
        }

        return new \Http\Response();
    }
}
