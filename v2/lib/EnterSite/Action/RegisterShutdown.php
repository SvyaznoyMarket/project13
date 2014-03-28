<?php

namespace EnterSite\Action;

use Enter\Http;
use EnterSite\Action;
use EnterSite\Controller;

class RegisterShutdown {
    /**
     * @param Http\Response|null $response
     * @param float $startAt
     */
    public function execute(Http\Response &$response = null, $startAt = null) {
        register_shutdown_function(function () use (&$response, $startAt) {
            if (!$response instanceof Http\Response) {
                $response = new Http\Response();
            }

            $error = error_get_last();
            if ($error && (error_reporting() & $error['type'])) {
                $response = (new Controller\Error\InternalServerError())->execute();
            }

            // logger
            (new Action\DumpLogger())->execute();
            $endAt = microtime(true);

            // debug info
            //(new Action\TimerDebug())->execute($response, $startAt, $endAt);
            //(new Action\Debug())->execute($response, $startAt, $endAt);

            // send response
            $response->send();
        });
    }
}