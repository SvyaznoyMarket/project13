<?php

namespace EnterSite\Action;

use Enter\Http;
use EnterSite\Action;

class HandleResponse {
    /**
     * @param \Enter\Http\Request $request
     * @param Http\Response|null $response
     */
    public function execute(Http\Request $request, Http\Response &$response = null) {
        // check redirect
        $response = (new Action\CheckRedirect())->execute($request);

        if (!$response) {
            // controller call
            $controllerCall = (new Action\MatchRoute())->execute($request);

            // response
            $response = call_user_func($controllerCall, $request);
        }
    }
}