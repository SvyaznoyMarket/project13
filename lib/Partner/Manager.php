<?php

namespace Partner;

class Manager {
    private $cookieName;
    private $cookieLifetime;
    private $cookieDomain;
    private $cookieArray = [];
    private $params4get = [
        'utm_source',
        'utm_content',
        'utm_term',
        'actionpay',    // actionpay
        'prx',          // cityads
        'aip',          // для cityads
        //'webmaster_id', // для actionpay, устарело?
        //'affiliate_id', // устарело?
    ];

    public function __construct() {
        $this->cookieName = \App::config()->partner['cookieName'];
        $this->cookieLifetime = \App::config()->partner['cookieLifetime'];
        $this->cookieDomain = \App::config()->session['cookie_domain'];
    }

    /**
     * @param \Http\Response $response
     */
    public function set(\Http\Response $response = null) {
        try {
            $request = \App::request();
            $cookie = null;
            $alreadyHasCookie = (bool) $request->cookies->get($this->cookieName);
            $lastPartner = null;

            $getParams = []; // TODO непонятная логика
            foreach( $this->params4get as $param ){
                $getParams[$param] = $request->get($param) ?: '';
            }

            $utmSource = $getParams['utm_source'];
            $utmMedium = $request->get('utm_medium');

            // SITE-3215
            $utmSourceCookie = \App::request()->cookies->get($this->cookieName);
            if (!$utmSource && !(bool)$utmSourceCookie) {
                $referer = parse_url($request->server->get('HTTP_REFERER'));
                $refererHost = $referer && !empty($referer['host']) ? $referer['host'] : null;

                // реферал пустой
                if (!empty($refererHost) && false === strpos($refererHost, 'enter')) {
                    // прямой трафик. уничтожаем партнерскую куку
                    if (\App::request()->cookies->has($this->cookieName)) {
                        $response->headers->clearCookie($this->cookieName, '/', null, $this->cookieDomain);
                    }

                    // реферал не пустой
                } elseif (!empty($refererHost)) {
                    // список поисковиков
                    $searchersList = [
                        'yandex.ru',
                        'google.ru',
                        'google.com',
                        'nova.rambler.ru',
                        'go.mail.ru',
                        'nigma.ru',
                        'webalta.ru',
                        'aport.ru',
                        'poisk.ru',
                        'km.ru',
                        'liveinternet.ru',
                        'quintura.ru',
                        'search.qip.ru',
                        'gde.ru',
                        'gogo.ru',
                        'ru.yahoo.com',
                        'images.yandex.ru',
                        'blogsearch.google.ru',
                        'blogs.yandex.ru',
                        'ru.search.yahoo.com',
                        'ya.ru',
                        'm.yandex.ru',
                    ];

                    $data = (array) \App::request()->cookies->get($this->cookieName, []);

                    // реферал находится в списке поисковиков
                    if (in_array($refererHost, $searchersList)) {
                        $data[] = $refererHost;
                        $this->cookieArray[] = new \Http\Cookie(
                            $this->cookieName,
                            $refererHost, time() + $this->cookieLifetime,
                            '/',
                            $this->cookieDomain,
                            false,
                            true
                        );

                        // ссылочный трафик
                    } else {
                        $data[] = $refererHost;
                        $this->cookieArray[] = new \Http\Cookie(
                            $this->cookieName,
                            $refererHost,
                            time() + $this->cookieLifetime,
                            '/',
                            $this->cookieDomain,
                            false,
                            true
                        );
                    }
                }
            }

            foreach( $getParams as $key => $value ){ // TODO непонятная логика
                if (!empty($value)) {
                    $this->cookieArray[] = new \Http\Cookie(
                        $key,
                        $value,
                        time() + $this->cookieLifetime,
                        '/',
                        $this->cookieDomain,
                        false,
                        true
                    );
                }
            }

            $sender = $request->get('sender');

            //(SmartEngine & SmartAssistant) & RetailRocket
            if ((bool)$sender) {
                $sender = explode('|', $sender); // ?sender=SmartEngine|product_id
                if ((bool)$sender[0] && (bool)$sender[1]) {
                    switch ($sender[0]) { // не забывать про строчные (маленькие) буквы
                        case \Smartengine\Client::NAME: {
                            \App::user()->setRecommendedProductByParams(
                                $sender[1], \Smartengine\Client::NAME, 'viewed_at', time()
                            );
                            break;
                        }
                        case \RetailRocket\Client::NAME: {
                            \App::user()->setRecommendedProductByParams(
                                $sender[1], \RetailRocket\Client::NAME, 'viewed_at', time()
                            );
                            break;
                        }
                    }
                }
            }

            $cookieValueArray = [
                'cityads' => \Partner\Counter\CityAds::NAME,
                'actionpay' => \Partner\Counter\Actionpay::NAME,
                'myragon' => \Partner\Counter\Myragon::NAME,
                //'tradetracker' => \Partner\Counter\Tradetracker::NAME,
                //'unilead' => \Partner\Counter\Unilead::NAME,
                //'leadgid' => \Partner\Counter\Leadgid::NAME,
                'yandex_market' => \Partner\PromoSource\YandexMarket::NAME,
                'pricelist' => \Partner\PromoSource\Pricelist::NAME,
                'criteo' => \Partner\PromoSource\Criteo::NAME,
                'sociomantic' => \Partner\PromoSource\Sociomantic::NAME,
                'flocktory' => \Partner\PromoSource\Flocktory::NAME,
            ];

            foreach ($cookieValueArray as $key => $value) {
                if (0 === strpos($utmSource, $key)) {
                    $lastPartner = $value;
                    $this->cookieArray[] = new \Http\Cookie(
                        $this->cookieName,
                        $value,
                        $this->cookieLifetime,
                        '/',
                        $this->cookieDomain,
                        false,
                        true
                    );
                    break;
                }
            }

            // Дополнительные куки или сложные условия
            if (0 === strpos($utmSource, 'cityads')) {
                $this->cookieArray[] = new \Http\Cookie(
                    'prx',
                    $request->get('prx'),
                    time() + $this->cookieLifetime,
                    '/',
                    $this->cookieDomain,
                    false,
                    true
                );
                $this->cookieArray[] = new \Http\Cookie(
                    'click_id',
                    $request->get('click_id'),
                    time() + $this->cookieLifetime,
                    '/',
                    $this->cookieDomain,
                    false,
                    true
                );
            // Actionpay
            } else if (0 === strpos($utmSource, 'actionpay')) {
                $this->cookieArray[] = new \Http\Cookie(
                    'actionpay',
                    $request->get('actionpay'),
                    time() + $this->cookieLifetime,
                    '/',
                    $this->cookieDomain,
                    false,
                    true
                );
            // Yandex
            }  else if (0 === strpos($utmSource, 'yandex') && $utmMedium && 0 === strpos($utmMedium, 'cpc')) {
                $lastPartner = \Partner\PromoSource\Yandex::NAME;
                $this->cookieArray[] = new \Http\Cookie(
                    $this->cookieName,
                    \Partner\PromoSource\Yandex::NAME,
                    time() + $this->cookieLifetime,
                    '/',
                    $this->cookieDomain,
                    false,
                    true
                );
            }

            // Google cpc
            if ($request->get('gclid')) {
                $lastPartner = \Partner\PromoSource\Google::NAME;
                $this->cookieArray[] = new \Http\Cookie(
                    $this->cookieName,
                    \Partner\PromoSource\Google::NAME,
                    time() + $this->cookieLifetime,
                    '/',
                    $this->cookieDomain,
                    false,
                    true
                );
            }

            foreach ($this->cookieArray as $cookie) {
                if ($cookie instanceof \Http\Cookie) {
                    if ($lastPartner) $response->headers->setCookie(new \Http\Cookie('last_partner', $lastPartner, 0, '/', \App::config()->session['cookie_domain']));
                    if ($cookie->getName() == $this->cookieName && $alreadyHasCookie) continue; // оставим существующую куку для трекинга изначального партнера
                    $response->headers->setCookie($cookie);
                }
            }

        } catch (\Exception $e) {
            \App::logger()->error($e, ['partner']);
        }
    }

    public function getName() {
        $request = \App::request();

        return $request->cookies->has('last_partner')
            ? $request->cookies->get('last_partner')
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
            /*case \Partner\Counter\Admitad::NAME:
            case \Partner\Counter\Admitad::NAME_SYNONYM:
                $return = [
                    $prefix => [\Partner\Counter\Admitad::NAME],
                    $prefix . '.' . \Partner\Counter\Admitad::NAME . '.cpamit_uid' => $request->cookies->get('cpamit_uid'),
                ];
                break;*/
            /*case \Partner\Counter\Recreative::NAME:
                $return = [
                    $prefix => [\Partner\Counter\Recreative::NAME],
                ];
                break;*/
            case \Smartengine\Client::NAME:
                $return = [
                    $prefix => [\Smartengine\Client::NAME],
                ];
                break;
            /*case \Partner\Counter\Reactive::NAME:
                $return = [
                    'name' => \Partner\Counter\Reactive::NAME,
                ];
                break;*/
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
            if ( !empty( $tmp ) ) {
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

        return array_merge_recursive($mainMeta, $mergedMeta, [$prefix => $partnerNames]);

    }

}