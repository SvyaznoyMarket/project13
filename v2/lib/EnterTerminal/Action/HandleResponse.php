<?php

namespace EnterTerminal\Action;

use Enter\Http;
use EnterTerminal\Action;

class HandleResponse {
    /**
     * @param \Enter\Http\Request $request
     * @param Http\Response|null $response
     */
    public function execute(Http\Request $request, Http\Response &$response = null) {
        if (!$response) {
            // controller call
            $controllerCall = (new Action\MatchRoute())->execute($request);

            // response
            $response = call_user_func($controllerCall, $request);
        }
    }
}