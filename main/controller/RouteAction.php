<?php

namespace Controller;

class RouteAction {
    public function execute(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        if (!$request->isXmlHttpRequest()) {
            throw new \Exception\NotFoundException('Request is not xml http request');
        }

        $router = \App::router();
        $resolver = \App::actionResolver();

        $actions = (array)$request->get('actions');
        if (!(bool)$actions) {
            throw new \Exception('Не перадан обязательный параметр actions');
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
                \App::logger()->info(['action' => $action], ['action']);

                $clonedRequest = clone $request;
                $clonedRequest->request->add($action['data']);
                $clonedRequest->attributes->add($router->match($action['url'], $action['method']));

                list($actionCall, $actionParams) = $resolver->getCall($clonedRequest);
                if (!is_array($actionCall)) {
                    throw new \Exception('Не получен обработчик запроса');
                }
                \App::logger()->info(['action' => get_class($actionCall[0]) . '::' . $actionCall[1], 'actionParams' => $actionParams], ['action']);

                /* @var $response \Http\Response|null */
                $response = call_user_func_array($actionCall, $actionParams);
                if ($response instanceof \Http\JsonResponse) {
                    $responseItem = json_decode($response->getContent());
                } else {
                    $responseItem = $response->getContent();
                }
            } catch (\Exception $e) {
                $responseItem = [
                    'success' => false,
                    'error'   => ['code' => $e->getCode(), 'message' => $e->getMessage()],
                ];
            }

            $responseData[] = $responseItem;
        }

        return new \Http\JsonResponse($responseData);
    }
}
