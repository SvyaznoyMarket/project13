<?php

namespace Partner;

use EnterQuery as Query;

class Manager {
    use \EnterApplication\CurlTrait;
    use \EnterQuery\ScmsQueryTrait;

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
        //'affiliate_id', // устарело?
    ];

    public function __construct() {
        $c = \App::config();
        $this->cookieName = $c->partner['cookieName'];
        $this->cookieLifetime = $c->partner['cookieLifetime'];
        $this->cookieDomain = $c->session['cookie_domain'];
    }

    /** Возвращает параметры партнера
     * @return array
     */
    public function setPartner() {

        $result = [
            'lastPartner'           => null,
            'lastPartnerCookieTime' => $this->cookieLifetime,
            'cookie'                => [],
        ];

        try {

            $request = \App::request();
            $cookie = null;
            $lastPartner = null;
            $freeHosts = [];
            $paidSources = [];

            $referer = parse_url($request->server->get('HTTP_REFERER'));
            $refererHost = $referer && !empty($referer['host']) ? $referer['host'] : null;

            $this->fixReferer($refererHost, $request);

            // ОСНОВНАЯ ЛОГИКА
            if (!preg_match('/(?:^|\.)(enter|ent3)\.(ru|loc)/', $refererHost)) {

                $partners = \App::scmsClient()->query('api/traffic-source');

                foreach ($partners as $partner) {
                    if ($partner['paid'] === true) {
                        $paidSources[] = $partner;
                    } else {
                        $freeHosts[] = $partner;
                    }
                }

                // Платные партнеры
                foreach ($paidSources as $source) {

                    $matchesCount = 0;
                    $nameSource = $source['token'];

                    if (!isset($source['matches']) || !is_array($source['matches'])) continue;

                    foreach ($source['matches'] as $matchArr) {
                        $matchKey = isset($matchArr['key']) ? $matchArr['key'] : null;
                        $matchValue = isset($matchArr['value']) ? $matchArr['value'] : null;
                        if ($matchValue !== null && $request->query->get($matchKey) === $matchValue) {
                            $matchesCount += 1;
                        }
                        else if ($matchValue === null && $request->query->has($matchKey)) {
                            $matchesCount += 1;
                        }
                    }

                    // если платный партнер
                    if ($matchesCount == count($source['matches'])) {
                        $lastPartner = $nameSource;

                        // ставим партнерские cookie
                        if (isset($source['cookies']) && is_array($source['cookies'])) {
                            foreach ($source['cookies'] as $partnerCookie) {
                                if ($request->query->has($partnerCookie['name'])) {
                                    $this->cookieArray[] = [
                                        'name'  => $partnerCookie['name'],
                                        'value' => $request->query->get($partnerCookie['name']),
                                        'time'  => in_array($source['token'], ['admitad', 'actionpay'], true) ? 60 * 60 * 24 * 45 :  $this->cookieLifetime,
                                    ];
                                }
                            }
                        }

                        if ($request->cookies->get($this->cookieName) != $lastPartner) {
                            $result['lastPartner'] = $lastPartner;
                            if (in_array($lastPartner, ['admitad', 'actionpay'], true)) {
                                $result['lastPartnerCookieTime'] = 60 * 60 * 24 * 45;
                            }
                        }
                    }
                }

                // Бесплатные партнеры
                if ($refererHost && $lastPartner === null && !$request->cookies->has($this->cookieName)) {
                    foreach ($freeHosts as $freeHost) {
                        $hostname = isset($freeHost['host_name']) ? $freeHost['host_name'] : null;

                        if ($hostname && preg_match('/' . $hostname . '/', $refererHost)) {
                            $lastPartner = $hostname;
                            // кука для отслеживания заказа
                            $this->cookieArray[] = [
                                'name'  => $this->cookieName,
                                'value' => $hostname,
                                'time'  => $this->cookieLifetime,
                            ];
                        }
                    }
                }

                // Рефералка
                if ($refererHost && $lastPartner === null && !$request->cookies->has($this->cookieName)) {
                    $this->cookieArray[] = [
                        'name'  => $this->cookieName,
                        'value' => $refererHost,
                        'time'  => $this->cookieLifetime,
                    ];
                }

            }

            $result['cookie'] = $this->cookieArray;

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
            case 'admitad':
                $return = [
                    $prefix => ['admitad'],
                    $prefix . '.admitad.admitad_uid' => $request->cookies->get('admitad_uid'),
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

    /** Хардкодный фикс, когда не передается referer, например переходы из баннеров внутри приложений
     *  Несмотря на это в список партнеров всё-равно необходимо заносить нужные правила
     * @param $ref
     * @param \Http\Request $request
     */
    private function fixReferer(&$ref, \Http\Request $request) {
        if ($request->query->get('utm_source') == 'skype') {
            $ref = 'skype_application';
        }
    }

}
