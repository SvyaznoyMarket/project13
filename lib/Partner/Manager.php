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

            $utmSource = $request->get('utm_source');

            // CityAds
            if (0 === strpos($utmSource, 'cityads')) {
                $response->headers->setCookie(new \Http\Cookie(
                    'prx',
                    $request->get('prx'),
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
            // eTargeting
            } if (0 === strpos($utmSource, 'etargeting')) {
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

    public function getMeta($name) {
        $return = [];

        $request = \App::request();

        $prefix = 'partner';
        switch ($name) {
            case \Partner\Counter\CityAds::NAME:
                $return = [
                    $prefix                                                 => [\Partner\Counter\CityAds::NAME],
                    $prefix . '.' . \Partner\Counter\CityAds::NAME . '.prx' => $request->cookies->get('prx'),
                ];
                break;
            case \Partner\Counter\Etargeting::NAME:
                $return = [
                    $prefix => [\Partner\Counter\Etargeting::NAME],
                ];
                break;
            case \Partner\Counter\Actionpay::NAME:
                $return = [
                    $prefix                                                         => [\Partner\Counter\Actionpay::NAME],
                    $prefix . '.' . \Partner\Counter\Actionpay::NAME . '.actionpay' => $request->cookies->get('actionpay'),
                ];
                break;
            case \Partner\Counter\Admitad::NAME:
                $return = [
                    $prefix                                                         => [\Partner\Counter\Admitad::NAME],
                    $prefix . '.' . \Partner\Counter\Admitad::NAME . '.admitad_uid' => $request->cookies->get('admitad_uid'),
                ];
                break;
            case \Partner\Counter\Recreative::NAME:
                $return = [
                    $prefix => [\Partner\Counter\Recreative::NAME],
                ];
                break;
            case \Partner\Counter\Reactive::NAME:
                $return = [
                    $prefix => [\Partner\Counter\Reactive::NAME],
                ];
                break;
        }

        return $return;
    }
}