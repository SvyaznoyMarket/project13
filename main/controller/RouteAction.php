<?php

namespace Controller;

class RouteAction {
    /**
     * @param \Http\Request $request
     * @return \Http\JsonResponse
     * @throws \Exception\NotFoundException
     */
    public function execute(\Http\Request $request) {
        //\App::logger()->debug('Exec ' . __METHOD__);

        if (!$request->isXmlHttpRequest()) {
            throw new \Exception\NotFoundException('Request is not xml http request');
        }

        $router = \App::router();
        $resolver = \App::actionResolver();

        $actions = (array)$request->get('actions');
        if (!(bool)$actions) {
            \App::logger()->error(['action' => __METHOD__, 'message' => 'Не передан обязательный параметр actions']);
            return new \Http\JsonResponse([
                'success' => false,
                'actions' => [],
            ]);
        }

        $actionData = [];
        foreach ($actions as $action) {
            $actionItem = [
                'success' => false,
            ];

            $action = array_merge([
                'url'    => null,
                'method' => 'GET',
                'data'   => [],
            ], (array)$action);
            if (!is_array($action['data'])) {
                $action['data'] = [];
            }

            if ( $urlGetPos = strpos( $action['url'], '?' ) ) {
                // Если строка содержит гет-параметры:
                $urlData = parse_url( $action['url'] );
                if ( !empty($urlData['query']) ) {
                    parse_str( $urlData['query'], $urlGetData );
                    $action['data'] = array_merge( $urlGetData, $action['data'] );
                }
                $action['url'] = substr( $action['url'], 0, $urlGetPos );
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
                    $actionItem = $response->getData();
                } else if ($response instanceof \Http\Response) {
                    $actionItem = $response->getContent();
                }
            } catch (\Exception $e) {
                $actionItem = [
                    'success' => false,
                    'error'   => ['code' => $e->getCode(), 'message' => $e->getMessage()],
                    'actions' => $actionData,
                ];
            }

            $actionData[] = $actionItem;
        }

        return new \Http\JsonResponse([
            'success' => true,
            'actions' => $actionData,
        ]);
    }
}
