<?php

namespace Partner;

use EnterQuery as Query;

class Manager {
    use \EnterApplication\CurlTrait;

    private $cookieName;
    private $secondClickCookieName;
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
        $c = \App::config();
        $this->cookieName = $c->partner['cookieName'];
        $this->secondClickCookieName = $c->partner['secondClickCookieName'];
        $this->cookieLifetime = $c->partner['cookieLifetime'];
        $this->cookieDomain = $c->session['cookie_domain'];
    }

    /**
     *
     */
    public function setPartner() {

        $result = [
            'lastPartner'   => null,
            'cookie'        => []
        ];

        try {

            $request = \App::request();
            $cookie = null;
            $lastPartner = null;

            $referer = parse_url($request->server->get('HTTP_REFERER'));
            $refererHost = $referer && !empty($referer['host']) ? $referer['host'] : null;

            // ОСНОВНАЯ ЛОГИКА
            if ($refererHost && !preg_match('/ent(er|3)\.(ru|loc)/', $refererHost)) {

                $partnerQuery = (new Query\CmsQuery('partner/paid-source.json'))->prepare();
                $hostQuery = (new Query\CmsQuery('partner/free-host.json'))->prepare();

                $this->getCurl()->execute();

                $paidSources = $partnerQuery->response;
                $freeHosts = $hostQuery->response;

                if (!is_array($paidSources) || !is_array($freeHosts)) {
                    throw new \Exception('Ошибка получения данных для партнеров из GIT CMS');
                }

                // Платные партнеры
                foreach ($paidSources as $nameSource => $sourceOptions) {

                    $matchesCount = 0;

                    if (!isset($sourceOptions['match']) || !is_array($sourceOptions['match'])) continue;

                    foreach ($sourceOptions['match'] as $matchKey => $matchValue) {
                        if ($matchValue !== null && 0 === strpos($request->query->get($matchKey), $matchValue)) {
                            $matchesCount += 1;
                        }
                        else if ($matchValue === null && $request->query->has($matchKey)) {
                            $matchesCount += 1;
                        }
                    }

                    // если платный партнер
                    if ($matchesCount == count($sourceOptions['match'])) {
                        $lastPartner = $nameSource;

                        // ставим партнерские cookie
                        if (isset($paidSources[$nameSource]['cookie']) && is_array($paidSources[$nameSource]['cookie'])) {
                            foreach ($paidSources[$nameSource]['cookie'] as $partnerCookieName) {
                                if ($request->query->has($partnerCookieName)) {
                                    $this->cookieArray[] = [
                                        'name'  => $partnerCookieName,
                                        'value' => $request->query->get($partnerCookieName),
                                        'time'  => $this->cookieLifetime
                                    ];
                                }
                            }
                        }

                        // если нет utm_source cookie или же она была проставлена не этим партнером
                        if ($request->cookies->get($this->cookieName) != $lastPartner) {
                            $result['lastPartner'] = $lastPartner;
                        }

                    }

                }

                // Бесплатные партнеры
                if ($lastPartner === null && !$request->cookies->has($this->cookieName)) {
                    foreach ($freeHosts as $freeHost) {
                        if (preg_match('/' . $freeHost . '/', $refererHost)) {
                            $lastPartner = $freeHost;
                            // кука для отслеживания заказа
                            $this->cookieArray[] = [
                                'name'  => $this->cookieName,
                                'value' => $freeHost
                            ];
                        }
                    }
                }

                // Рефералка
                if ($lastPartner === null && !$request->cookies->has($this->cookieName)) {
                    $this->cookieArray[] = [
                        'name'  => $this->cookieName,
                        'value' => $request->cookies->has($this->cookieName) && in_array($request->cookies->get($this->cookieName), array_keys($paidSources))
                            ? $request->cookies->get($this->cookieName)
                            : $refererHost
                        ];
                }

            }

            foreach ($this->cookieArray as $cookie) {
                $result['cookie'][] = $cookie;
            }

        } catch (\Exception $e) {
            \App::logger()->error($e, ['partner']);
            \App::exception()->remove($e);
        }

        return $result;
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
            if ($product->getRootCategory()) {
                $return[$keyName . '.category'] = $product->getRootCategory()->getId();
            }
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
        if (!is_array($partners) || !count($partners) || !$product) return [];

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
