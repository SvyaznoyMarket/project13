<?php

namespace Controller\User;


class PrivateAction {

    public function __construct() {
        if (!\App::user()->getToken() || !\App::config()->user['enabled']) {
            throw new \Exception\AccessDeniedException();
        }
    }

}