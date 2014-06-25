<?php

namespace EnterSite\Action;

use Enter\Http;

class HandleError {
    /**
     * @param $error
     */
    public function execute(&$error) {
        set_error_handler(function($code, $message, $file, $line) use (&$error) {
            switch ($code) {
                case E_USER_ERROR:
                    $error = new \ErrorException($message, 0, $code, $file, $line);
                    return true;

                case E_WARNING:
                    throw new \ErrorException($message, 0, $code, $file, $line);
            }

            return false;
        });
    }
}