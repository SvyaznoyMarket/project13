<?php

namespace EnterSite\Action;

use Enter\Http;

class HandleResponse {
    /**
     * @param Http\Response|null $response
     */
    public function execute(Http\Response &$response = null) {
        if (!$response) {
            return;
        }
    }
}