<?php

namespace EnterSite\Controller;

use Enter\Http;
use EnterSite\ConfigTrait;
use EnterSite\LoggerTrait;
use EnterSite\RouterTrait;
use EnterSite\Action;

class Router {
    use ConfigTrait, LoggerTrait, RouterTrait {
        ConfigTrait::getConfig insteadof LoggerTrait, RouterTrait;
    }

    /**
     * @param Http\Request $request
     * @return Http\JsonResponse
     * @throws \Exception
     */
    public function execute(Http\Request $request) {
        $logger = $this->getLogger();
        $matchRouteAction = new Action\MatchRoute();

        $actions = (array)$request->data['actions'];
        if (!(bool)$actions) {
            throw new \Exception('Не передан параметр actions');
        }

        $actionResponses = [];
        foreach ($actions as $actionId => $action) {
            $actionResponse = [];

            $action = array_merge([
                'url'    => null,
                'method' => 'GET',
                'data'   => [],
            ], (array)$action);

            try {
                $newRequest = new Http\Request(
                    (array)parse_url($action['url'], PHP_URL_QUERY),
                    (array)$action['data'],
                    (array)$request->cookies,
                    [],
                    array_merge((array)$request->server, [
                        'REQUEST_URI'    => parse_url($action['url'], PHP_URL_PATH),
                        'REQUEST_METHOD' => $action['method'],
                    ])
                );

                /** @var callable $controllerCall */
                $controllerCall = $matchRouteAction->execute($newRequest);

                $response = call_user_func($controllerCall, $newRequest);
                if ($response instanceof Http\JsonResponse) {
                    $actionResponse = $response->data;
                } else if ($response instanceof Http\Response) {
                    $actionResponse = $response->content;
                }
            } catch (\Exception $e) {
                $actionResponse = [
                    'error'   => ['code' => $e->getCode(), 'message' => $e->getMessage()],
                ];
            }

            $actionResponses[$actionId] = $actionResponse;
        }

        return new Http\JsonResponse([
            'result' => $actionResponses,
        ]);
    }
}