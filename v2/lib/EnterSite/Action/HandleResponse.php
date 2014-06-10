<?php

namespace EnterSite\Action;

use Enter\Http;
use EnterSite\Action;
use EnterSite\ConfigTrait;
use EnterSite\LoggerTrait;

class HandleResponse {
    use ConfigTrait, LoggerTrait {
        ConfigTrait::getConfig insteadof LoggerTrait;
    }

    /**
     * @param \Enter\Http\Request $request
     * @param Http\Response|null $response
     */
    public function execute(Http\Request $request, Http\Response &$response = null) {
        $config = $this->getConfig();
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

        // debug cookie
        try {
            if (
                $response
                && (
                    ($request->cookies['debug'] && !$config->debug)
                    || (!$request->cookies['debug'] && $config->debug)
                )
            ) {
                $cookie = new Http\Cookie(
                    'debug',
                    $config->debug ? 1 : 0,
                    strtotime('+7 days' ),
                    '/',
                    null,
                    false,
                    false
                );
                $response->headers->setCookie($cookie);
            }
        } catch (\Exception $e) {
            $logger->push(['type' => 'error', 'action' => __METHOD__, 'error'  => $e]);
        }
    }
}