<?php

namespace Controller\User;

class IndexAction {
    public function __construct() {
        if (!\App::user()->getToken()) {
            throw new \Exception\AccessDeniedException();
        }
    }

    public function execute(\Http\Request $request) {

        return new \Http\RedirectResponse(\App::router()->generate(\App::config()->user['defaultRoute']));

    }
}