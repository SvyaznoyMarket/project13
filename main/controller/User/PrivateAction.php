<?php

namespace Controller\User;


class PrivateAction {

    public function __construct() {
        if (!\App::user()->getEntity() || !\App::config()->user['enabled']) {
            throw new \Exception\AccessDeniedException();
        }
    }

}