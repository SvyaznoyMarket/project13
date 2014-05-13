<?php

namespace EnterTerminal\Action;

use Enter\Http;
use EnterTerminal\ConfigTrait;
use EnterTerminal\Action;

class HandleResponse {
    use ConfigTrait;

    /**
     * @param \Enter\Http\Request $request
     * @param Http\Response|null $response
     * @throws \Exception
     */
    public function execute(Http\Request $request, Http\Response &$response = null) {
        $config = $this->getConfig();

        if ($request) {
            $config->clientId = is_scalar($request->query['clientId']) ? $request->query['clientId'] : null;
            if (!$config->clientId) {
                throw new \Exception('Не указан параметр clientId');
            }

            $config->coreService->clientId = $config->clientId;
        }

        if (!$response) {
            // controller call
            $controllerCall = (new Action\MatchRoute())->execute($request);

            // response
            $response = call_user_func($controllerCall, $request);
        }
    }
}