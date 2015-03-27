<?php

namespace Controller;

class MobileRedirectAction {
    /**
     * @param \Http\Request $request
     * @throws \Exception\NotFoundException
     * @return \Http\Response
     */
    public function execute(\Http\Request $request) {
        //\App::logger()->debug('Exec ' . __METHOD__);

        $config = \App::config();

        $routeName = $request->attributes->get('route');

        $hasRedirect = $routeName && in_array($routeName, [
            'cart',
            'homepage',
            'product.category',
            'product',
            'user.login',
        ]);
        if (!$hasRedirect) {
            return null;
        }

        $mobileDetect = new \Mobile_Detect();
        if (!$mobileDetect->isMobile()) {
            return null;
        }

        //$redirectUrl = str_replace($config->mainHost, $config->mobileHost, $request->getSchemeAndHttpHost()) . $request->getRequestUri();
        $redirectUrl = strtr($request->getSchemeAndHttpHost(), [
            $config->mainHost => $config->mobileHost,
            ':8080'           => '', //FIXME: костыль для nginx-а
        ]) . $request->getRequestUri();

        $response =  new \Http\RedirectResponse($redirectUrl, 302);
        $response->headers->setCookie(new \Http\Cookie(
            \App::session()->getName(),
            \App::session()->getId(),
            time() + \App::config()->session['cookie_lifetime'],
            '/',
            \App::config()->session['cookie_domain'],
            false,
            true
        ));

        return $response;
    }
}
