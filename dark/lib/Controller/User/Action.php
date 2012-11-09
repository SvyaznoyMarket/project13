<?php

namespace Controller\User;

class Action {
    public function login(\Http\Request $request) {
        if (\App::user()->getEntity()) {
            return new \Http\RedirectResponse(\App::router()->generate('user'));
        }


    }

    public function logout() {
        \App::user()->removeToken();

        return new \Http\RedirectResponse(\App::router()->generate('homepage'));
    }

    public function register(\Http\Request $request) {

    }

    public function forgot(\Http\Request $request) {

    }

    public function reset(\Http\Request $request) {

    }

    public function changePassword(\Http\Request $request) {
        if ($request->isMethod('post')) {

        }
    }
}