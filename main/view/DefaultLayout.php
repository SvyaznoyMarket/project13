<?php

namespace View;

use Helper\TemplateHelper;
use Session\AbTest\ABHelperTrait;
use Session\AbTest\AbTest;
use Model\Product\Category\Entity as Category;

class DefaultLayout extends Layout {
    use ABHelperTrait;

    protected $layout  = 'layout-oneColumn';
    /** @var bool */
    // TODO переделать на автоопределение
    protected $useTchiboAnalytics = false;
    /** @var bool */
    protected $useMenuHamburger = false;

    /**
     * Flocktory precheckout data
     *
     * @var array
     */
    protected $flPrecheckoutData = [];

    public function __construct() {
        parent::__construct();

        // Меню нужно в нескольких рендерингах, поэтому запрашиваем его сразу

        if ('on' !== \App::request()->headers->get('SSI')) {
            $this->setGlobalParam('menu', (new Menu($this))->generate(\App::user()->getRegion()));
        }

        $this->setTitle('Enter - это выход!');
        $this->addMeta('yandex-verification', '623bb356993d4993');
        $this->addMeta('viewport', 'width=900');
        //$this->addMeta('title', 'Enter - это выход!');
        $this->addMeta('description', \App::config()->description);
        
        if ($this->getSort()) {
            $this->addMeta('robots', 'noindex, follow');
        }

        // TODO: осторожно, говнокод
        if ('live' != \App::$env) {
            $this->addMeta('apple-itunes-app', 'app-id=486318342,affiliate-data=, app-argument=');
        }
        /* Meta and Title могут быть переопределены в методе prepare() в /main/controller/Main/IndexAction.php
            — загружаются там из json для главной страницы, например.
        */

        $this->addStylesheet(\App::config()->debug ? '/css/global.css' : '/css/global.min.css');

        $this->addStylesheet(\App::config()->debug ? '/styles/global.css' : '/styles/global.min.css');

        $this->addJavascript(\App::config()->debug ? '/js/loadjs.js' : '/js/loadjs.min.js');
    }

    /**
     * @return bool
     */
    public function isMenuHamburger()
    {
        return $this->useMenuHamburger;
    }

    /**
     * Является ли категория "чибовской"
     *
     * @param Category $category
     * @return bool
     */
    public function isTchiboCategory(Category $category)
    {
        return isset($category->getAncestor()[0]) && $category->getAncestor()[0]->getUi() === Category::UI_TCHIBO;
    }

    public function slotRelLink() {
        $request = \App::request();
        $url = $request->getRequestUri();
        $path = explode('?', $url);
        $path = reset($path);
        if ('/' == $path) {
            $path = '';
        }

        $sort = $this->getSort();

        return '<link rel="canonical" href="' . $request->getScheme() . '://' . \App::config()->mainHost . $path . ($sort ? '?' . $sort : '') . '" />';
    }

    /**
     * @return string
     */
    protected function getSort() {
        return '';
    }

    protected function getPrevNextRelLinks(array $additionalParams = []) {
        $request = \App::request();
        /** @var \Iterator\EntityPager $productPager */
        $productPager = $this->getParam('productPager') instanceof \Iterator\EntityPager ? $this->getParam('productPager') : null;
        $urlHost = $request->getScheme() . '://' . \App::config()->mainHost;
        $params = array_merge($request->routePathVars->all(), $additionalParams);

        $relLinks = [];

        if ($productPager->getPage() > 1) {
            $relLinks[] = '<link rel="prev" href="' . $urlHost . \App::router()->generateUrl($request->routeName, array_merge($params, ['page' => $productPager->getPage() - 1])) . '" />';
        }

        if ($productPager->getPage() < $productPager->getLastPage()) {
            $relLinks[] = '<link rel="next" href="' . $urlHost . \App::router()->generateUrl($request->routeName, array_merge($params, ['page' => $productPager->getPage() + 1])) . '" />';
        }

        return implode("\n", $relLinks);
    }

    public function slotGoogleAnalytics() {
        return $this->tryRender('_googleAnalytics');
    }

    /** Наименования шаблона для load.js
     * @return string
     */
    public function slotBodyDataAttribute() {
        return 'default';
    }

    /** Класс у body
     * @return string
     */
    public function slotBodyClassAttribute() {
        return ' body-new';
    }

    public function slotContentHead() {
        // заголовок контента страницы
        if (!$this->hasParam('title')) {
            $this->setParam('title', null);
        }
        // навигация
        if (!$this->hasParam('breadcrumbs')) {
            $this->setParam('breadcrumbs', []);
        }

        return $this->render('_contentHead', $this->params);
    }

    public function slotContent() {
        return '';
    }

    public function slotSidebar() {
        return '';
    }

    public function slotBottombar() {
        return '';
    }

    /**
     * @return string
     */
    public function slotHeadJavascript() {
        $return = "\n";
        foreach ([
            \App::config()->debug ? 'http://yandex.st/jquery/1.8.3/jquery.js' : 'http://yandex.st/jquery/1.8.3/jquery.min.js',

            \App::config()->debug ? '/js/vendor/LAB.js' : '/js/prod/LAB.min.js',

            \App::config()->debug ? '/js/vendor/modernizr.custom.js' : '/js/prod/modernizr.custom.min.js',

        ] as $javascript) {
            $return .= '<script src="' . $javascript . '" type="text/javascript"></script>' . "\n";
        }

        $return .= $this->render('_headJavascript');

        return $return;
    }

    /** Большое количество JS-кода партнеров в подвале
     * @return string
     */
    public function slotInnerJavascript() {
        return $this->render('_innerJavascript');
    }

    /** Google Remarketing Code (standard tag)
     * @link https://developers.google.com/adwords-remarketing-tag/
     * @param array $tagParams
     * @return string|null
     */
    public function slotGoogleRemarketingJS($tagParams = []) {

        $tagParams = array_merge(['pagetype' => 'default'], $tagParams);

        return \App::config()->googleAnalytics['enabled']
            ? $this->tryRender('_remarketingGoogle', ['tag_params' => $tagParams])
            : null;
    }

    public function slotAuth() {
        // SITE-3676
        return (!in_array(\App::request()->routeName, ['user.login', 'user.register'])) ? $this->render('_auth', ['oauthEnabled' => \App::config()->oauthEnabled]) : '';

//        return ('user.login' != \App::request()->routeName) ? $this->render('_auth') : '';
    }

    /** Статичный юзербар (над меню)
     * @return string
     */
    public function slotTopbar() {
        return $this->render('userbar2/topbar');
    }

    /** Всплывающий юзербар
     * @return string
     */
    public function slotUserbar() {
        return $this->render('common/_userbar');
    }

    /** Панель поиска
     * @return string
     */
    public function slotSearchBar() {
        return $this->render('common/_searchbar');
    }

    /** Строка поиска
     * @return string
     */
    public function slotNavigation() {
        if ('on' === \App::request()->headers->get('SSI')) {
            return \App::helper()->render(
                '__ssi-cached',
                [
                    'path'  => '/navigation',
                    'query' => [
                        'regionId' => \App::user()->getRegion()->id ?: \App::config()->region['defaultId'],
                    ],
                ]
            );
        } else {
            return $this->render('common/_navigation', ['menu' => $this->getGlobalParam('menu')]);
        }
    }

    public function slotUserbarContent() {
        return '';
    }

    public function slotUserbarContentData() {
        return '';
    }

    public function slotUpper() {
        return (new \Helper\TemplateHelper())->render('common/__upper');
    }

    public function slotYandexMetrika() {

        if (\App::config()->yandexMetrika['enabled']) {
            // загрузка основного или тестового счетчика
            return in_array(\App::config()->mainHost, ['www.enter.ru', 'm.enter.ru']) ? $this->render('_yandexMetrika') : $this->render('_yandexMetrikaTest');
        }

    }

    public function slotMetaOg() {
        return '';
    }

    public function slotMicroformats() {
        $breadcrumbs = $this->getParam('breadcrumbs');

        return
            ($breadcrumbs ? '<script type="application/ld+json">' . json_encode(call_user_func(function() use($breadcrumbs) {
                return [
                    '@context' => 'http://schema.org/',
                    '@type' => 'BreadcrumbList',
                    'itemListElement' => call_user_func(function() use($breadcrumbs) {
                        $result = [];
                        $position = 0;
                        foreach ($breadcrumbs as $path) {
                            if (!$path['url']) {
                                continue;
                            }

                            $position++;
                            $result[] = [
                                '@type' => 'ListItem',
                                'position' => $position,
                                'item' =>
                                [
                                    '@id' => (!preg_match('/^[a-z9-0\-_]\:/is', $path['url']) ? \App::request()->getScheme() . '://' . \App::config()->mainHost : '') . $path['url'],
                                    'name' => $path['name']
                                ]
                            ];
                        }

                        return $result;
                    }),
                ];
            }), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n" : '') .

            '<script type="application/ld+json">' . json_encode(call_user_func(function() {
                return [
                    '@context' => 'http://schema.org',
                    '@type' => 'Organization',
                    'name' => 'Интернет-магазин Enter.ru',
                    'url' => \App::request()->getScheme() . '://' . \App::config()->mainHost . $this->url('homepage'),
                    'logo' => \App::request()->getScheme() . '://' . \App::config()->mainHost . '/images/logo.png',
                    'sameAs' => [
                        'https://www.facebook.com/enter.ru',
                        'https://twitter.com/enter_ru',
                        'https://vk.com/public31456119',
                        'https://www.youtube.com/user/EnterLLC',
                        'https://ok.ru/enterllc'
                    ]
                ];
            }), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n"
        ;
    }

    /**
     * Слот с партнёрами-счётчиками, вызывается на всех страницах сайта
     *
     * @return string
     */
    public function slotPartnerCounter() {
        $return = '';

        if (\App::config()->analytics['enabled']) {
            $routeName = \App::request()->routeName;
            $routeToken = \App::request()->routePathVars->get('token');

            // на всех страницах сайта, кроме...
            if (!in_array($routeName, [
                'product',
                'orderV3',
                'cart',
            ])) {
                if (\App::config()->partners['CityAdsRetargeting']['enabled']) $return .= "\n\n" . '<div id="xcntmyAsync" class="jsanalytics"></div>';
            }

            // Реактив (adblender) SITE-5718
            call_user_func(function() use ($routeName, &$return) {
                if (!\App::config()->partners['Adblender']['enabled']) return;

                $template = '<div id="adblenderJS" class="jsanalytics" data-value="{{dataValue}}"></div>';
                $dataValue = [];
                if ('orderV3.complete' === $routeName) {
                    return;
                } else if ('cart' === $routeName) {
                    $dataValue['type'] = 'cart';
                } else {
                    $dataValue['type'] = 'default';
                }

                $return .= strtr($template, [
                    '{{dataValue}}' => $this->json($dataValue),
                ]);
            });

            if ($routeToken === 'subscribe_friends') {
                $return .= $this->tryRender('partner-counter/_actionpay_subscribe');
                $return .= $this->tryRender('partner-counter/_cityAds_subscribe');
            }

            // ActionPay ретаргетинг
            if (\App::config()->partners['ActionpayRetargeting']['enabled']) {
                $return .= '<div id="ActionPayJS" data-vars="' .
                    $this->json( (new \View\Partners\ActionPay($routeName, $this->params))->execute() ) . '" class="jsanalytics"></div>';
            }

            // вызов JS Alexa-кода
            if (\App::config()->partners['alexa']['enabled']) {
                $return .= '<div id="AlexaJS" class="jsanalytics"></div><noscript><img src="https://d5nxst8fruw4z.cloudfront.net/atrk.gif?account=mPO9i1acVE000x" style="display:none" height="1" width="1" alt="" /></noscript>';
            }

            if (\App::config()->partners['facebook']['enabled']) {
                $return .= strtr('<div id="facebookJs" class="jsanalytics" data-value="{{dataValue}}"></div>', [
                    '{{dataValue}}' => $this->json(['id' => \App::config()->facebookOauth->clientId]),
                ]);
            }

            $return .= $this->googleAnalyticsJS();
            $return .= $this->flocktoryScriptJS();
            $return .= $this->slotMyThings(['route' => $routeName]);
        }

        $return .= $this->tryRender('partner-counter/livetex/_slot_liveTex');
        $return .= $this->slotSociaPlus();

        return $return;
    }

    protected function flocktoryScriptJS() {
        $html = '';

        if (\App::config()->flocktory['precheckout'] || \App::config()->flocktory['postcheckout']) {
            if (\App::config()->flocktory['precheckout']) {

                $helper = new TemplateHelper();

                // user
                call_user_func(function() use(&$html, $helper) {
                    $attributes = '';
                    $userEntity = \App::user()->getEntity();

                    if ($userEntity) {
                        if ($userEntity->getEmail()) {
                            $attributes .= 'data-fl-user-email="' . $helper->escape($userEntity->getEmail()) . '" ';
                        }

                        if ($userEntity->getName()) {
                            $attributes .= 'data-fl-user-name="' . $helper->escape($userEntity->getName()) . '" ';
                        }
                    }

                    if (!$userEntity) {
                        $attributes .= 'data-fl-action="precheckout" ';
                        $attributes .= 'data-fl-spot="no_enterprize_reg" ';
                    }

                    if ($attributes) {
                        $html .= '<div class="i-flocktory" ' . $attributes . '></div>';
                    }
                });

                call_user_func(function() use(&$html, $helper) {
                    $attributes = '';
                    foreach ($this->flPrecheckoutData as $key => $value) {
                        $attributes .= 'data-' . $helper->escape($key) . '="' . $helper->escape($value) . '" ';
                    }

                    $html .= '<div class="i-flocktory" ' . $attributes . '></div>';
                });
            }

            $html .= sprintf('<div id="flocktoryScriptJS" class="jsanalytics" data-vars="%s" ></div>', \App::config()->flocktory['site_id']);
        }

        return $html;
    }

    public function googleAnalyticsJS(){

        $routeName = \App::request()->routeName;

        // new Google Analytics Code
        $useTchiboAnalytics = false;
        if (\App::config()->googleAnalyticsTchibo['enabled']) {
            $useTchiboAnalytics = $this->useTchiboAnalytics;
            if (!$useTchiboAnalytics && $this->getGlobalParam('isTchibo')) {
                $useTchiboAnalytics = $this->getGlobalParam('isTchibo', false);
            }
        }

        return '<div id="gaJS" class="jsanalytics"
                    data-vars="' . $this->json((new \View\Partners\GoogleAnalytics($routeName, $this->params))->execute()) . '"
                    data-use-tchibo-analytics="' . $useTchiboAnalytics . '">
                </div>';
    }

    public function slotConfig() {
        return $this->tryRender('_config');
    }

    public function slotMyThings($data) {
        if (\App::config()->partners['MyThings']['enabled']) {
            $data = array_merge(['EventType' => 'Visit'], $data);
            return sprintf('<div id="MyThingsJS" class="jsanalytics" data-value="%s"></div>', $this->json($data));
        }
        return '';
    }

    public function slotCriteo() {
        return $this->render('partner-counter/_criteo', ['criteoData' => (new \View\Partners\Criteo($this->params))->execute()]);
    }


    public function slotRetailRocket() {
        $routeName = \App::request()->routeName;

        $rrObj = new \View\Partners\RetailRocket($routeName);

        $rrData = null;
        if ($routeName == 'product') {
            $rrData = $rrObj->product($this->getParam('product'));
        } elseif ($routeName == 'product.category') {
           $rrData = $rrObj->category($this->getParam('category'));
        } elseif ($routeName == 'orderV3.complete') {
            if (!$this->getParam('sessionIsReaded')) {
                $rrData = $rrObj->transaction($this->getParam('orders'));
            }
        }

        $rrData['emailCookieName'] = \App::config()->partners['RetailRocket']['userEmail']['cookieName'];

        $return = '';
        $return .= '<div id="RetailRocketJS" class="jsanalytics"';
        if ($rrData) {
            $return .= ' data-value="' . $this->json($rrData) . '"';
        }
        $return .= '></div>';

        return $return;
    }

    public function slotSociaPlus() {
        return \App::config()->partners['Sociaplus']['enabled'] ? '<div id="sociaPlusJs" class="jsanalytics"></div>' : '';
    }


    public function slotMarinLandingPageTagJS() {
        if (!\App::config()->partners['marin']['enabled']) return '';
        return '<div id="marinLandingPageTagJS" class="jsanalytics">
            <noscript><img src="https://tracker.marinsm.com/tp?act=1&cid=7saq97byg0&script=no" ></noscript></div>';
    }


    public function slotMarinConversionTagJS() {
        return '';
    }

    public function slotAdFoxBground() {
        $viewParams = $this->getParam('viewParams');
        $show = (bool) ( $viewParams && isset($viewParams['showSideBanner']) ) ? $viewParams['showSideBanner'] : true;
        if (false == $show) return;

        $routeToken = \App::request()->routePathVars->get('token');
        if (
            !\App::config()->adFox['enabled'] ||
            ($routeToken == 'subscribers')
        ) {
            return;
        }

        return '<div class="adfoxWrapper" id="adfoxbground"></div>';
    }

    public function slotCallback() {
        $status = \App::abTest()->getCallbackStatus();
        if (!\App::config()->userCallback['enabled'] || ('disabled' === $status)) {
            return '';
        }

        return \App::helper()->render('common/__callback', ['view' => $status]);
    }

    /** Google Tag Manager Container (ports.js)
     * @param array $data Дополнительные данные для GTM
     * @return string
     * @link https://developers.google.com/tag-manager/
     */
    public function slotGoogleTagManagerJS($data = []) {

        $containerId = \App::config()->googleTagManager['containerId'];

        if (!\App::config()->googleTagManager['enabled'] || !\App::config()->analytics['enabled'] || !$containerId) return '';

        return
            '<div id="googleTagManagerJS" class="jsanalytics" data-value="' . \App::config()->googleTagManager['containerId'] . '">
                <script>var dataLayerGTM = '. json_encode($data, JSON_UNESCAPED_UNICODE) .';</script>
                <!-- Google Tag Manager -->
                <noscript><iframe src="//www.googletagmanager.com/ns.html?id=' . $containerId . '" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
                <!-- End Google Tag Manager -->
            </div>';
    }

    public function slotAdvMakerJS() {
        return '';
    }

    public function slotInsiderJS(){
        return \App::config()->partners['Insider']['enabled'] ? '<div id="insiderJS" class="jsanalytics"></div>' :  '';
    }

    public function slotRevolverJS() {
        if (!\App::config()->partners['Revolver']['enabled']) return '';
        return '
            <script type="text/javascript">
            <!--//--><![CDATA[//><!--
            var advaction_params = advaction_params || {};
            advaction_params.asite = "enter.ru";
            (function()
            {
            var aa = document.createElement("script");
            aa.type = "text/javascript";
            aa.async = true;
            aa.src = document.location.protocol+"//advaction.ru/js/advertiser.js";
            var s = document.getElementsByTagName("script")[0];
            s.parentNode.insertBefore(aa, s);
            }
            )();
            //-->!]>]
            </script>';

    }

    /* Дополнение для Revolver JS на странице заказа */
    public function revolverOrdersJS($orders) {
        /* Revolver может принимать только один заказ */
        /** @var  $order \Model\Order\Entity */
        $order = reset($orders);
        $orderNumber = $order->getNumberErp();
        $orderSum = $order->getSum();
        return "
            <script type=\"text/javascript\">
                <!--//--><![CDATA[//><!--
                var asite = 'enter.ru';
                var order_id = '$orderNumber';
                var price = '$orderSum';
                (function(){
                    var aa = document.createElement(\"script\");
                    aa.type = \"text/javascript\";
                    aa.async = true;
                    aa.src = document.location.protocol+\"//advaction.ru/js/ec_action.js\";
                    var s = document.getElementsByTagName(\"script\")[0];
                    s.parentNode.insertBefore(aa, s);
                })();
                //-->!]>]
            </script>";
    }

    public function slotGetIntentJS() {
        if (!\App::config()->partners['GetIntent']['enabled']) {
            return '';
        }

        return '<div id="GetIntentJS" class="jsanalytics" data-value="' . $this->json([]) . '"></div>';
    }

    public function slotGifteryJS() {
        return \App::config()->partners['Giftery']['enabled']
            ? '<div id="gifteryJS" class="jsanalytics"></div>'
            : '';
    }

    public function slotSolowayJS() {
        if (!\App::config()->partners['soloway']['enabled']) {
            return '';
        }

        return '<div id="solowayJS" class="jsanalytics"></div>';
    }

    public function slotAdmitadJS() {}
}
