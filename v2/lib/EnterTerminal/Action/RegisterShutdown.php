<?php

namespace EnterTerminal\Action;

use Enter\Http;
use EnterSite\Action;
use EnterTerminal\Controller;

class RegisterShutdown {
    /**
     * @param Http\Request|null $request
     * @param Http\Response|null $response
     * @param float $startAt
     */
    public function execute(Http\Request &$request = null, Http\Response &$response = null, $startAt = null) {
        register_shutdown_function(function () use (&$request, &$response, $startAt) {
            if (!$response instanceof Http\Response) {
                $response = new Http\JsonResponse();
            }

            $error = error_get_last();
            if ($error && (error_reporting() & $error['type'])) {
                //$response = (new Controller\Error\InternalServerError())->execute($request);
            }

            // logger
            (new Action\DumpLogger())->execute();

            $endAt = microtime(true);

            // debug info
            (new Action\Debug())->execute($request, $response, $startAt, $endAt);

            // send response
            $response->send();
        });
    }
}