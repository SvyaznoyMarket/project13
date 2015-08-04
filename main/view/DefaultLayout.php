<?php

namespace View;

use Session\AbTest\AbTest;

class DefaultLayout extends Layout {
    protected $layout  = 'layout-oneColumn';
    protected $breadcrumbsPath = null;
    protected $useTchiboAnalytics = false;

    public function __construct() {
        parent::__construct();

        // Меню нужно в нескольких рендерингах, поэтому запрашиваем его сразу
        $this->setGlobalParam('menu', (new Menu($this))->generate_new(\App::user()->getRegion()));

        $this->setTitle('Enter - это выход!');
        $this->addMeta('yandex-verification', '623bb356993d4993');
        $this->addMeta('viewport', 'width=900');
        //$this->addMeta('title', 'Enter - это выход!');
        $this->addMeta('description', \App::config()->description);

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

    public function slotRelLink() {
        $request = \App::request();

        $tmp = explode('?', $request->getRequestUri());
        $tmp = reset($tmp);
        $path = str_replace(array('_filter', '_tag'), '', $tmp);
        if ('/' == $path) {
            $path = '';
        }


        $relLink = $request->getScheme() . '://' . \App::config()->mainHost . $path;

        return '<link rel="canonical" href="' . $relLink . '" />';
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

    public function slotHeader() {
        $subscribeForm = (array)\App::dataStoreClient()->query('/subscribe-form.json');

        \App::dataStoreClient()->execute();

        if (!isset($subscribeForm['mainText'])) {
            $subscribeForm['mainText'] = 'Подпишитесь на рассылку и будьте в курсе акций, скидок и суперцен!';
        }

        if (!isset($subscribeForm['inputText'])) {
            $subscribeForm['inputText'] = 'Введите Ваш e-mail';
        }

        if (!isset($subscribeForm['buttonText'])) {
            $subscribeForm['buttonText'] = 'Подписаться';
        }

        return $this->render('_header',
            $this->params + ['subscribeForm' => $subscribeForm]
        );
    }

    public function slotSeoContent() {
        return $this->render('_seoContent', $this->params);
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
        return (!in_array(\App::request()->attributes->get('route'), ['user.login', 'user.register'])) ? $this->render('_auth', ['oauthEnabled' => \App::config()->oauthEnabled]) : '';

//        return ('user.login' != \App::request()->attributes->get('route')) ? $this->render('_auth') : '';
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
        return $this->render('common/_navigation-new', ['menu' => $this->getGlobalParam('menu')]);
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

    public function slotBanner() {
        return '';
    }

    /**
     * Слот с партнёрами-счётчиками, вызывается на всех страницах сайта
     *
     * @return string
     */
    public function slotPartnerCounter() {
        $return = '';

        if (\App::config()->analytics['enabled']) {
            $routeName = \App::request()->attributes->get('route');
            $routeToken = \App::request()->attributes->get('token');

            // на всех страницах сайта, кроме...
            if (!in_array($routeName, [
                'product',
                'order',
                'order.complete',
                'cart',
            ])) {
                if (\App::config()->partners['SmartLeads']['enabled']) $return .= "\n\n" . '<div id="xcntmyAsync" class="jsanalytics"></div>';
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

            if ('subscribe_friends' == $routeToken) {
                $return .= $this->tryRender('partner-counter/_actionpay_subscribe');
                $return .= $this->tryRender('partner-counter/_cityAds_subscribe');
            }

            // ActionPay ретаргетинг
            if (\App::config()->partners['ActionpayRetargeting']['enabled']) $return .= '<div id="ActionPayJS" data-vars="' .
                $this->json( (new \View\Partners\ActionPay($routeName, $this->params))->execute() ) . '" class="jsanalytics"></div>';

            // вызов JS Alexa-кода
            if (\App::config()->partners['alexa']['enabled']) {
                $return .= '<div id="AlexaJS" class="jsanalytics"></div><noscript><img src="https://d5nxst8fruw4z.cloudfront.net/atrk.gif?account=mPO9i1acVE000x" style="display:none" height="1" width="1" alt="" /></noscript>';
            }

            $return .= $this->googleAnalyticsJS();

            if (\App::config()->partners['TagMan']['enabled']) {
                $return .= '<div id="TagManJS" class="jsanalytics"></div>';
            }

            $return .= $this->slotMyThings(['route' => $routeName]);
        }

        $return .= $this->tryRender('partner-counter/livetex/_slot_liveTex');
        $return .= $this->slotSociaPlus();

        return $return;
    }

    public function googleAnalyticsJS(){

        $routeName = \App::request()->attributes->get('route');

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

    public function slotSociomantic() {
        if (!\App::config()->partners['sociomantic']['enabled']) return '';
        $smantic_path = 'partner-counter/sociomantic/';
        $routeName = \App::request()->attributes->get('route');
        $breadcrumbs = $this->getBreadcrumbsPath();
        $region_id = \App::user()->getRegion()->getId();
        $smantic = new \View\Partners\Sociomantic($region_id);

        $prod = null;
        $prod_cats = null;
        $cart_prods = null;
        $return = '';

        if ( in_array( $routeName, ['order', 'order.complete'] ) ) {
            return;
        }

        // на всех остальных страницах сайта // необходимо установить наш код главной страницы (inclusion tag)
        $return .= $this->render($smantic_path . '01-homepage');

        if ($routeName == 'product.category') {

            $category = $this->getParam('category') instanceof \Model\Product\Category\Entity ? $this->getParam('category') : null;
            if ($category) {
                $prod_cats = $smantic->makeCategories($breadcrumbs, $category, 'category');
                $return .= $this->render($smantic_path . 'smanticPage', ['prod_cats' => $prod_cats]);
            }

        } else if ($routeName == 'product') {

            $product = $this->getParam('product') instanceof \Model\Product\Entity ? $this->getParam('product') : null;
            if ( $product ) {
                /** @var $product \Model\Product\Entity */
                $category = $product->getRootCategory();
                $categories = $product->getCategory();
                if (!$category) $category = reset($categories);
                $prod_cats = array_map(function($a){ return $a->getName(); }, $categories);
                $prod = $smantic->makeProdInfo($product, $prod_cats);
                $return .= $this->render($smantic_path . 'smanticPage', ['prod' => $prod, 'prod_cats' => $prod_cats]);
            }

        } else if ($routeName == 'cart') {

            $products = $this->getParam('products');
            $cartProductsById = $this->getParam('cartProductsById');
            if ($products && $cartProductsById) {
                $cart_prods = $smantic->makeCartProducts($products, $cartProductsById);
                $return .= $this->render($smantic_path . 'smanticPage', ['cart_prods' => $cart_prods]);
            }

        } else if ($routeName == 'tchibo') {
            $return .= $this->render($smantic_path . 'smanticPage', ['prod_cats' => ['Tchibo']]);
        }

        return !empty($return) ? $return : false;
    }

    public function slotCriteo() {
        return $this->render( 'partner-counter/_criteo',
            ['criteoData' =>  (new \View\Partners\Criteo($this->params))->execute()] );
    }


    public function slotRetailRocket() {
        $routeName = \App::request()->attributes->get('route');
        if ('orderV3.complete' === $routeName) {
            $routeName = 'order.complete';
        }

        $rrObj = new \View\Partners\RetailRocket($routeName);

        $rrData = null;
        if ($routeName == 'product') {
            $rrData = $rrObj->product($this->getParam('product'));
        } elseif ($routeName == 'product.category') {
           $rrData = $rrObj->category($this->getParam('category'));
        } elseif ($routeName == 'order.complete') {
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


    public function slotEnterleads() {
        $routeToken = \App::request()->attributes->get('token');
        $onPages = [
            'internet_price',
            'subscribe_friends',
            'enter-friends'
        ];
        if ( !in_array($routeToken, $onPages) ) return;

        return '<div id="enterleadsJS" class="jsanalytics" ></div>';
    }


    public function slotMarinLandingPageTagJS() {
        if (!\App::config()->partners['marin']['enabled']) return '';
        return '<div id="marinLandingPageTagJS" class="jsanalytics">
            <noscript><img src="https://tracker.marinsm.com/tp?act=1&cid=7saq97byg0&script=no" ></noscript></div>';
    }


    public function slotMarinConversionTagJS() {
        return '';
    }

    public function slotСpaexchangeJS () {
        return '';
    }

    /**
     * Сpaexchange. Конверсионный пиксель.
     * Данный пиксель устанавливается на страницу «спасибо за заказ»
     */
    public function slotСpaexchangeConversionJS () {
        return '';
    }

    public function slotAdFoxBground() {
        $viewParams = $this->getParam('viewParams');
        $show = (bool) ( $viewParams && isset($viewParams['showSideBanner']) ) ? $viewParams['showSideBanner'] : true;
        if (false == $show) return;

        $routeToken = \App::request()->attributes->get('token');
        if (
            !\App::config()->adFox['enabled'] ||
            ($routeToken == 'subscribers')
        ) {
            return;
        }

        return '<div class="adfoxWrapper" id="adfoxbground"></div>';
    }



    public function getBreadcrumbsPath() {
        if (null !== $this->breadcrumbsPath) {
            return $this->breadcrumbsPath;
        }

        $category = $this->getParam('category');
        if (!($category instanceof \Model\Product\Category\Entity)) {
            return false;
        }

        $breadcrumbs = [];
        foreach ($category->getAncestor() as $ancestor) {
            $link = $ancestor->getLink();
            if (\App::request()->get('shop')) $link .= (false === strpos($link, '?') ? '?' : '&') . 'shop='. \App::request()->get('shop');
            $breadcrumbs[] = array(
                'name' => $ancestor->getName(),
                'url'  => $link,
            );
        }
        $link = $category->getLink();
        if (\App::request()->get('shop')) $link .= (false === strpos($link, '?') ? '?' : '&') . 'shop='. \App::request()->get('shop');
        $breadcrumbs[] = array(
            'name' => $category->getName(),
            'url'  => $link,
        );

        return $this->breadcrumbsPath = $breadcrumbs;
    }


    public function slotEnterprizeConfirmJs() {
        return '';
    }

    public function slotEnterprizeCompleteJs() {
        return '';
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

    public function slotEnterprizeRegJS() {
        return '';
    }

    public function slotAdvMakerJS() {
        return '';
    }

    public function slotHubrusJS() {
        return \App::config()->partners['Hubrus']['enabled'] ? '<div id="hubrusJS" class="jsanalytics"></div>' :  '';
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

    /** Дополнительный hidden-input с id-категории в форму поиска
     * @return string
     */
    public function blockInputCategory() {
        return AbTest::isAdvancedSearch()
            ? '<input type="hidden" name="category" data-bind="value: currentCategory() == null ? 0 : currentCategory().id, disable: currentCategory() == null " />'
            : null;
    }

    public function slotGifteryJS() {
        if (!\App::config()->partners['Giftery']['enabled']) return '';
        return <<<EOL
        <!-- BEGIN GIFTERY CODE {literal} -->
        <script type="text/javascript">
        (function(){
        var s = document.createElement('script');s.type = 'text/javascript';s.async = true;
        s.src = '//widget.giftery.ru/js/114550/11456/';
        var ss = document.getElementsByTagName('script')[0];ss.parentNode.insertBefore(s, ss);
        })();
        </script>
        <!-- {/literal} END GIFTERY CODE -->
EOL;
    }
}
