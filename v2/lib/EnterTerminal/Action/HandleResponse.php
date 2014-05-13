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
     */
    public function execute(Http\Request $request, Http\Response &$response = null) {
        $config = $this->getConfig();

        if ($request) {
            $config->clientId = is_scalar($request->query['clientId']) ? $request->query['clientId'] : null;
            $config->shopId = is_scalar($request->query['shopId']) ? $request->query['shopId'] : null;

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