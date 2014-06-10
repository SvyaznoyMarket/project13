<?php

namespace EnterSite\Action;

use Enter\Http;
use EnterSite\LoggerTrait;
use EnterSite\Action;
use EnterSite\Controller;

class RegisterShutdown {
    use LoggerTrait;

    /**
     * @param Http\Request|null $request
     * @param Http\Response|null $response
     * @param \Exception|null $error
     * @param float $startAt
     */
    public function execute(Http\Request &$request = null, Http\Response &$response = null, &$error = null, $startAt = null) {
        register_shutdown_function(function () use (&$request, &$response, &$error, $startAt) {
            if (!$response instanceof Http\Response) {
                $response = new Http\Response();
            }

            // logger
            (new Action\DumpLogger())->execute();

            $lastError = error_get_last();
            if ($lastError && (error_reporting() & $lastError['type'])) {
                $response = (new Controller\Error\InternalServerError())->execute($request);
                $this->getLogger()->push(['type' => 'error', 'error' => $lastError, 'tag' => ['critical']]);
            }

            if ($error) {
                $response->statusCode = Http\Response::STATUS_INTERNAL_SERVER_ERROR;
                $this->getLogger()->push(['type' => 'error', 'error' => $error, 'tag' => ['critical']]);
            }

            $endAt = microtime(true);

            // debug info
            (new Action\Debug())->execute($request, $response, $error, $startAt, $endAt);

            // send response
            $response->send();
        });
    }
}