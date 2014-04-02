<?php

namespace EnterSite\Action;

use Enter\Http;

class HandleError {
    /**
     * @param Http\Response|null $response
     */
    public function execute(Http\Response &$response = null) {
        set_error_handler(function($code, $message, $file, $line) use (&$response) {
            switch ($code) {
                case E_USER_ERROR:
                    if ($response instanceof Http\Response) {
                        $response->statusCode = Http\Response::STATUS_INTERNAL_SERVER_ERROR;
                    }

                    return true;

                case E_WARNING:
                    throw new \ErrorException($message, 0, $code, $file, $line);
            }

            return false;
        });
    }
}