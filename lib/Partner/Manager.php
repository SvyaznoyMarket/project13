<?php

namespace Partner;

class Manager {
    private $cookieName;
    private $cookieLifetime;

    public function __construct() {
        $this->cookieName = \App::config()->partner['cookieName'];
        $this->cookieLifetime = \App::config()->partner['cookieLifetime'];
    }

    /**
     * @param \Http\Response $response
     */
    public function set(\Http\Response $response = null) {
        try {
            $request = \App::request();
            $cookie = null;

            // admitad
            if ($userId = $request->get('admitad_uid')) {
                $response->headers->setCookie(new \Http\Cookie(
                    'admitad_uid',
                    $userId,
                    time() + $this->cookieLifetime,
                    '/',
                    null,
                    false,
                    true //false // важно httpOnly=false, чтобы js мог получить куку
                ));

                $cookie = new \Http\Cookie(
                    $this->cookieName,
                    \Partner\Counter\Admitad::NAME,
                    time() + $this->cookieLifetime,
                    '/',
                    null,
                    false,
                    true //false // важно httpOnly=false, чтобы js мог получить куку
                );
            } else if ($utmSource = $request->get('utm_source')) {
                if (0 === strpos($utmSource, 'etargeting')) {
                    $cookie = new \Http\Cookie(
                        $this->cookieName,
                        \Partner\Counter\Etargeting::NAME,
                        time() + $this->cookieLifetime,
                        '/',
                        null,
                        false,
                        true //false // важно httpOnly=false, чтобы js мог получить куку
                    );
                }
            }

            if ($cookie instanceof \Http\Cookie) {
                $response->headers->setCookie($cookie);
            }
        } catch (\Exception $e) {
            \App::logger()->error($e);
        }
    }

    public function getName() {
        $request = \App::request();

        return $request->cookies->has($this->cookieName)
            ? $request->cookies->get($this->cookieName)
            : null;
    }
}