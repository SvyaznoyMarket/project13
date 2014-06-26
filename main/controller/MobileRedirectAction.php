<?php

namespace Controller;

class MobileRedirectAction {
    /**
     * @param \Http\Request $request
     * @throws \Exception\NotFoundException
     * @return \Http\Response
     */
    public function execute(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $config = \App::config();

        $routeName = $request->attributes->get('route');

        $hasRedirect = $routeName && in_array($routeName, [
            'cart',
            'homepage',
            'product.category',
            'product',
        ]);
        if (!$hasRedirect) {
            return null;
        }

        $classFile = $config->appDir . '/vendor/Mobile-Detect/Mobile_Detect.php';
        if (!file_exists($classFile)) {
            \App::logger()->error('Класс Mobile_Detect не найден', ['mobile']);

            return null;
        }
        include_once $classFile;

        $mobileDetect = new \Mobile_Detect();
        if (!$mobileDetect->isMobile()) {
            return null;
        }

        //$redirectUrl = str_replace($config->mainHost, $config->mobileHost, $request->getSchemeAndHttpHost()) . $request->getRequestUri();
        $redirectUrl = strtr($request->getSchemeAndHttpHost(), [
            $config->mainHost => $config->mobileHost,
            ':8080'           => '', //FIXME: костыль для nginx-а
        ]) . $request->getRequestUri();

        $response =  new \Http\RedirectResponse($redirectUrl, 301);

        /*
        if ($mobileHost = \App::config()->mobileHost) {
            $cookie = new \Http\Cookie(
                \App::config()->authToken['authorized_cookie'],
                0, //cookieValue
                time() + \App::config()->session['cookie_lifetime'],
                '/',
                $mobileHost,
                false,
                false
            );
            $response->headers->setCookie($cookie);
        }
        */

        return $response;
    }
}
