<?php

namespace Partner;

class Manager {
    private $cookieName;
    private $cookieLifetime;
    private $cookieNames = [];
    private $params4get = [ 'utm_source','utm_content','utm_term', 'prx', 'aip', 'webmaster_id', 'admitad_uid' ];

    public function __construct() {
        $this->cookieName = \App::config()->partner['cookieName'];
        $this->cookieLifetime = \App::config()->partner['cookieLifetime'];
        $this->cookieNames[\Partner\Counter\MyThings::NAME] = \App::config()->myThings['cookieName'];
    }

    /**
     * @param \Http\Response $response
     */
    public function set(\Http\Response $response = null) {
        try {
            $request = \App::request();
            $cookie = null;
            //$session = \App::session(); // Можно сделать и через сессию

            $getParams = [];
            foreach( $this->params4get as $param ){
                $getParams[$param] = $request->get($param) ?: '';
            }

            $utmSource = $getParams['utm_source'];

            foreach( $getParams as $key => $value ){
                if (!empty($value)) {
                    $response->headers->setCookie(new \Http\Cookie(
                        $key,
                        $value, time() + $this->cookieLifetime, '/', null, false, true
                    ));
                    // $session->remove($key); $session->set($key, $value); // Можно сделать и через сессию
                }
            }

            // for debug:
            //print " |## Testng Cookie and session: \n";
            //print_r( $response->headers->get('utm_content') );
            //print_r( $request->cookies->get('utm_content') );
            //print " | ";
            //print_r( $session->get('utm_content') );
            //print " ##| \n";

            $sender = $request->get('sender');

            //SmartEngine & SmartAssistant
            if ((bool)$sender) {
                $sender = explode('|', $sender); // ?sender=SmartEngine|product_id
                if ((bool)$sender[0] && (bool)$sender[1]) {
                    switch ($sender[0]) {
                        case \Smartengine\Client::NAME: {
                            \App::user()->setRecommendedProductByParams($sender[1], \Smartengine\Client::NAME, 'viewed_at', time());
                            break;
                        }
                    }
                }
            }

            // myThings
            if (0 === strpos($utmSource, 'mythinqs')) {
                $cookie = new \Http\Cookie(
                    $this->cookieNames[\Partner\Counter\MyThings::NAME],
                    \Partner\Counter\MyThings::NAME,
                    time() + $this->cookieLifetime,
                    '/',
                    null,
                    false,
                    true
                );
            }
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

    public function getMeta($name, \Model\Product\Entity $product = null) {
        $return = [];

        $request = \App::request();

        $prefix = 'partner';
        switch ($name) {
            case \Partner\Counter\CityAds::NAME:
                $return = [
                    $prefix => [\Partner\Counter\CityAds::NAME],
                    $prefix . '.' . \Partner\Counter\CityAds::NAME . '.prx' => $request->cookies->get('prx'),
                ];
                break;
            case \Partner\Counter\Actionpay::NAME:
                $return = [
                    $prefix => [\Partner\Counter\Actionpay::NAME],
                    $prefix . '.' . \Partner\Counter\Actionpay::NAME . '.actionpay' => $request->cookies->get('actionpay'),
                ];
                break;
            case \Partner\Counter\Admitad::NAME:
                $return = [
                    $prefix => [\Partner\Counter\Admitad::NAME],
                    $prefix . '.' . \Partner\Counter\Admitad::NAME . '.admitad_uid' => $request->cookies->get('admitad_uid'),
                ];
                break;
            case \Partner\Counter\Recreative::NAME:
                $return = [
                    $prefix => [\Partner\Counter\Recreative::NAME],
                ];
                break;
            case \Partner\Counter\MyThings::NAME:
                $return = [
                    $prefix => [\Partner\Counter\MyThings::NAME],
                ];
                break;
            case \Smartengine\Client::NAME:
                $return = [
                    $prefix => [\Smartengine\Client::NAME],
                ];
                break;
            case \Partner\Counter\Reactive::NAME:
                $return = [
                    'name' => \Partner\Counter\Reactive::NAME,
                ];
                break;
        }

        if ((bool)$product) {
            $keyName = $prefix . '.' . $name;
            if ($product->getArticle()) {
                $keyName .= '.article.' . $product->getArticle();
            } elseif (is_array($product->getEan()) && count($product->getEan())) {
                $keyName .= '.ean.' . $product->getEan()[0];
            } else {
                $keyName .= '.id.' . $product->getId();
            }
            if ($product->getMainCategory()) $return[$keyName . '.category'] = $product->getMainCategory()->getId();
        }


        foreach ($this->params4get as $param) {
            $tmp = $request->get($param) ? : $request->cookies->get($param);
            if (!empty( $tmp )) {
                $return[$param] = $tmp;
                //$return[$tmp] = $session->get($tmp); // Можно сделать и через сессию
            }
        }

        return $return;
    }

    public function fabricateMetaByPartners($partners = [], $product = null) {
        if (!is_array($partners) || !count($partners) || !$product) return false;

        $return = [];
        $prefix = 'partner';
        $partnerNames = [];

        foreach ($partners as $partnerName) {
            $partnerData = $this->getMeta($partnerName, $product);
            if (isset($partnerData[$prefix])) {
                $partnerNames[$partnerData[$prefix][0]] = 1;
                unset($partnerData[$prefix]);
            }
            $return = array_merge($return, $partnerData);
        }

        return array_merge($return, [$prefix => array_keys($partnerNames)]);
    }

    public function fabricateCompleteMeta($mainMeta, $mergedMeta) {
        if (!$mainMeta) return $mergedMeta;
        $prefix = 'partner';
        $partnerNames = array_unique(array_merge( isset($mainMeta[$prefix]) ? $mainMeta[$prefix] : [], isset($mergedMeta[$prefix]) ? $mergedMeta[$prefix] : []));
        if (isset($mainMeta[$prefix])) unset($mainMeta[$prefix]);
        if (isset($mergedMeta[$prefix])) unset($mergedMeta[$prefix]);

        // tmp debug
        //$file = 'utm_attribs.txt'; file_put_contents($file, print_r($mainMeta,1), LOCK_EX);
        //$file = 'utm_attribs2.txt'; file_put_contents($file, print_r($mergedMeta,1), LOCK_EX);

        return array_merge_recursive($mainMeta, $mergedMeta, [$prefix => $partnerNames]);

    }

}