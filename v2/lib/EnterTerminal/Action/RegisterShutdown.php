<?php

namespace EnterTerminal\Action;

use Enter\Http;
use EnterSite\Action;
use EnterTerminal\Controller;

class RegisterShutdown {
    /**
     * @param Http\Request|null $request
     * @param Http\Response|null $response
     * @param \Exception|null $error
     * @param float $startAt
     */
    public function execute(Http\Request &$request = null, Http\Response &$response = null, &$error = null, $startAt = null) {
        register_shutdown_function(function () use (&$request, &$response, &$error, $startAt) {
            if (!$response instanceof Http\Response) {
                $response = new Http\JsonResponse();
            }

            $lastError = error_get_last();
            if ($lastError && (error_reporting() & $lastError['type'])) {
                //$response = (new Controller\Error\InternalServerError())->execute($request);
                $response->statusCode = Http\Response::STATUS_INTERNAL_SERVER_ERROR;
                $response->data['error'] = ['code' => 500, 'message' => isset($lastError['message']) ? $lastError['message'] : ''];
            }

            /*
            if ($error) {
                $response->statusCode = Http\Response::STATUS_INTERNAL_SERVER_ERROR;
                $response->data['error'] = ['code' => 500, 'message' => $error->getMessage()];
            }
            */

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