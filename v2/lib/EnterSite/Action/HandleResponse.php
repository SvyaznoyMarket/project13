<?php

namespace EnterSite\Action;

use Enter\Http;
use EnterSite\Action;
use EnterSite\LoggerTrait;

class HandleResponse {
    use LoggerTrait;

    /**
     * @param \Enter\Http\Request $request
     * @param Http\Response|null $response
     */
    public function execute(Http\Request $request, Http\Response &$response = null) {
        $logger = $this->getLogger();

        $logger->push(['request' => [
            'uri'    => $request->getRequestUri(),
            'query'  => $request->query,
            'data'   => $request->data,
            'cookie' => $request->cookies,
        ], 'action' => __METHOD__, 'tag' => ['request']]);

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