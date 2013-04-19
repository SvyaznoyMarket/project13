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

            // CityAds
            if ('ca' == $request->get('ref') && ($prx = $request->get('prx'))) {
                $response->headers->setCookie(new \Http\Cookie(
                    'prx',
                    $prx,
                    time() + $this->cookieLifetime,
                    '/',
                    null,
                    false,
                    true
                ));

                $cookie = new \Http\Cookie(
                    $this->cookieName,
                    \Partner\Counter\CityAds::NAME,
                    time() + $this->cookieLifetime,
                    '/',
                    null,
                    false,
                    true
                );
            } else if ($utmSource = $request->get('utm_source')) {
                // eTargeting
                if (0 === strpos($utmSource, 'etargeting')) {
                    $cookie = new \Http\Cookie(
                        $this->cookieName,
                        \Partner\Counter\Etargeting::NAME,
                        time() + $this->cookieLifetime,
                        '/',
                        null,
                        false,
                        true
                    );
                // Actionpay
                } else if (0 === strpos($utmSource, 'actionpay')) {
                    $response->headers->setCookie(new \Http\Cookie(
                        'actionpay',
                        $request->get('actionpay'),
                        time() + $this->cookieLifetime,
                        '/',
                        null,
                        false,
                        true
                    ));

                    $cookie = new \Http\Cookie(
                        $this->cookieName,
                        \Partner\Counter\Actionpay::NAME,
                        time() + $this->cookieLifetime,
                        '/',
                        null,
                        false,
                        true
                    );
                // Admitad
                } else if (0 === strpos($utmSource, 'admitad')) {
                    $response->headers->setCookie(new \Http\Cookie(
                        'admitad_uid',
                        $request->get('admitad_uid'),
                        time() + $this->cookieLifetime,
                        '/',
                        null,
                        false,
                        true
                    ));

                    $cookie = new \Http\Cookie(
                        $this->cookieName,
                        \Partner\Counter\Admitad::NAME,
                        time() + $this->cookieLifetime,
                        '/',
                        null,
                        false,
                        true
                    );
                // Recreative
                } else if (0 === strpos($utmSource, 'recreative')) {
                    $cookie = new \Http\Cookie(
                        $this->cookieName,
                        \Partner\Counter\Recreative::NAME,
                        time() + $this->cookieLifetime,
                        '/',
                        null,
                        false,
                        true
                    );
                // Reactive
                } else if ((0 === strpos($utmSource, 'vk.com')) && (0 === strpos($request->get('utm_campaing'), 'social_target'))) {
                    $cookie = new \Http\Cookie(
                        $this->cookieName,
                        \Partner\Counter\Reactive::NAME,
                        time() + $this->cookieLifetime,
                        '/',
                        null,
                        false,
                        true
                    );
                }
            }

            if ($cookie instanceof \Http\Cookie) {
                $response->headers->setCookie($cookie);
            }
        } catch (\Exception $e) {
            \App::logger()->error($e, ['partner']);
        }
    }

    public function getName() {
        $request = \App::request();

        return $request->cookies->has($this->cookieName)
            ? $request->cookies->get($this->cookieName)
            : null;
    }
}