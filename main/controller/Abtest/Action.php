<?php

namespace Controller\Abtest;

class Action {
    public function setCookie(\Http\Request $request, $redirectTo) {
        $case = \App::abTest()->getCase();

        $cookie = new \Http\Cookie(
            \App::config()->abtest['cookieName'],
            $case->getKey(),
            strtotime(\App::config()->abtest['bestBefore']),
            '/',
            null,
            false,
            false // важно httpOnly=false, чтобы js мог получить куку
        );

        if ('/' !== $redirectTo[0]) {
            $redirectTo = '/' . $redirectTo;
        }

        if ((bool)$request->getQueryString()) {
            $redirectTo .= '?' . $request->getQueryString();
        }

        $response = new \Http\RedirectResponse($redirectTo);
        $response->headers->setCookie($cookie);

        return $response;
    }
}