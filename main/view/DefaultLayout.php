<?php

namespace View;

class DefaultLayout extends Layout {
    protected $layout  = 'layout-twoColumn';
    protected $breadcrumbsPath = null;

    public function __construct() {
        parent::__construct();

        $this->setTitle('Enter - это выход!');
        $this->addMeta('yandex-verification', '623bb356993d4993');
        $this->addMeta('viewport', 'width=900');
        //$this->addMeta('title', 'Enter - это выход!');
        $this->addMeta('description', 'Enter - новый способ покупать. Любой из ' . \App::config()->product['totalCount'] . ' товаров нашего ассортимента можно купить где угодно, как угодно и когда угодно. Наша миссия: дарить время для настоящего. Честно. С любовью. Как для себя.');

        // TODO: осторожно, говнокод
        if ('live' != \App::$env) {
            $this->addMeta('apple-itunes-app', 'app-id=486318342,affiliate-data=, app-argument=');
        }
        /* Meta and Title могут быть переопределены в методе prepare() в /main/controller/Main/IndexAction.php
            — загружаются там из json для главной страницы, например.
        */

        $this->addStylesheet('/css/global.min.css');

        $this->addStylesheet('/styles/global.min.css');

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


        $relLink = $request->getSchemeAndHttpHost() . $path;

        return '<link rel="canonical" href="' . $relLink . '" />';
    }

    public function slotGoogleAnalytics() {
        return $this->tryRender('_googleAnalytics');
    }

    public function slotKissMetrics() {
        return $this->tryRender('_kissMetrics');
    }

    public function slotBodyDataAttribute() {
        return 'default';
    }

    public function slotBodyClassAttribute() {
        return '';
    }

    public function slotHeader() {
        return $this->render('_header', $this->params);
    }

    public function slotSeoContent() {
        return $this->render('_seoContent', $this->params);
    }

    public function slotFooter() {
        $client = \App::contentClient();

        $response = null;
        $client->addQuery(
            'footer_default',
            [],
            function($data) use (&$response) {
                $response = $data;
            },
            function(\Exception $e) {
                \App::exception()->add($e);
            }
        );
        $client->execute();

        $response = array_merge(['content' => ''], (array)$response);

        $response['content'] = str_replace('8 (800) 700-00-09', \App::config()->company['phone'], $response['content']);


        return $response['content'];
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

    public function slotRegionSelection() {
        /** @var $regions \Model\Region\Entity */
        $regions = $this->getParam('regionsToSelect', null);

        if (null === $regions) {
            try {
                $regions = \RepositoryManager::region()->getShownInMenuCollection();
            } catch (\Exception $e) {
                \App::logger()->error($e);

                $regions = [];
            }
        }

        return $this->render('_regionSelection', array_merge($this->params, array('regions' => $regions)));
    }

    /**
     * @return string
     */
    public function slotHeadJavascript() {
        $return = "\n";
        foreach ([
            \App::config()->debug ? 'http://code.jquery.com/jquery-1.8.3.js' : 'http://yandex.st/jquery/1.8.3/jquery.min.js',
            '/js/prod/LAB.min.js',

            \App::config()->debug ? '/js/vendor/html5.js' : '/js/prod/html5.min.js',
            
        ] as $javascript) {
            $return .= '<script src="' . $javascript . '" type="text/javascript"></script>' . "\n";
        }

        $return .= $this->render('_headJavascript');

        return $return;
    }



    public function slotInnerJavascript() {
        $return = ''
            . $this->render('_remarketingGoogle', ['tag_params' => []])
            . "\n\n"
            . $this->render('_innerJavascript');

        return $return;
    }


    public function slotAuth() {
        return ('user.login' != \App::request()->attributes->get('route')) ? $this->render('_auth') : '';
    }

    public function slotUserbar() {
        return '';
    }

    public function slotUserbarContent() {
        return '';
    }

    public function slotUserbarContentData() {
        return '';
    }

    public function slotYandexMetrika() {
        return (\App::config()->yandexMetrika['enabled']) ? $this->render('_yandexMetrika') : '';
    }

    public function slotMyThings() {
        return (\App::config()->analytics['enabled'] && (bool)$this->getParam('myThingsData')) ? $this->render('_myThingsTracker', array('myThingsData' => $this->getParam('myThingsData'),)) : '';
    }

    public function slotMetaOg() {
        return '';
    }

    public function slotAdvanceSeoCounter() {
        return '';
    }

    public function slotAdriver() {
        return \App::config()->analytics['enabled'] ? "<div id=\"adriverCommon\"  class=\"jsanalytics\"></div>\r\n" : '';
    }

    public function slotMainMenu() {
        $renderer = \App::closureTemplating();

        $catalogJsonBulk = \RepositoryManager::productCategory()->getCatalogJsonBulk();
        $promoHtmlBulk = \RepositoryManager::productCategory()->getPromoHtmlBulk($catalogJsonBulk);

        if (\App::config()->requestMainMenu) {
            $client = \App::curl();

            $isFailed = false;
            $content = '';
            $client->addQuery('http://' . \App::config()->mainHost . \App::router()->generate('category.mainMenu'), [], function($data) use (&$content, &$isFailed) {
                isset($data['content']) ? $content = $data['content'] : $isFailed = true;
            }, function(\Exception $e) use (&$isFailed) {
                \App::exception()->remove($e);
                $isFailed = true;
            }, 2);
            $client->execute(1, 2);

            if ($isFailed) {
                $content = $renderer->render('__mainMenu', [
                    'menu'            => (new Menu())->generate(),
                    'catalogJsonBulk' => $catalogJsonBulk,
                    'promoHtmlBulk'   => $promoHtmlBulk,
                ]);
            }
        } else {

            \Debug\Timer::start('main-menu');
            $content = $renderer->render('__mainMenu', [
                'menu'            => (new Menu())->generate(),
                'catalogJsonBulk' => $catalogJsonBulk,
                'promoHtmlBulk'   => $promoHtmlBulk,
            ]);
            \Debug\Timer::stop('main-menu');

            //\App::debug()->add('time.main-menu', round(\Debug\Timer::get('main-menu')['total'], 3) * 1000, 95);
        }

        return $content;
    }


    public function slotBrandMenu() {
        $renderer = \App::closureTemplating();
        $content = '';

        if ($this->getParam('category') instanceof \Model\Product\Category\Entity) {
            $category = $this->getParam('category');
            /** @var $category \Model\Product\Category\Entity */
            $categoryToken = $category->getToken();
            $catalogJsonBulk = \RepositoryManager::productCategory()->getCatalogJsonBulk();
            $relatedCategoryJson = empty($catalogJsonBulk[$categoryToken]['related_category_main']) ?
                    null :
                    $catalogJsonBulk[$categoryToken]['related_category_main'];

            if ($relatedCategoryJson && !empty($relatedCategoryJson['related_category_token'])) {
                $relatedCategory = \RepositoryManager::productCategory()->getEntityByToken(trim((string)$relatedCategoryJson['related_category_token']));
                if (!$relatedCategory) return '';
                \RepositoryManager::productCategory()->prepareEntityBranch($relatedCategory, \App::user()->getRegion());
                \App::coreClientV2()->execute();
                if (!$relatedCategory) return '';

                $categoryList = [];
                foreach ($relatedCategory->getChild() as $child) {
                    $categoryList[] = [
                        'name'  => $child->getName(),
                        'link'  => $child->getLink(),
                    ];
                }

                $content .= $renderer->render('__brandMenu', [
                    'css'           => isset($relatedCategoryJson['css']) ? $relatedCategoryJson['css'] : '',
                    'brandLogo'     => isset($relatedCategoryJson['logo_path']) ? $relatedCategoryJson['logo_path'] : '',
                    'categoryName'  => $relatedCategory->getName(),
                    'categoryList'  => $categoryList,
                ]);
            } // end of if (json has related_category_token)

        } // end of if (is category page)

        return $content;
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
                $return .= "\n\n" . $this->tryRender('partner-counter/_cityads');
            }

            // на всех страницах сайта, кроме shop.*
            if ((0 !== strpos($routeName, 'shop')) && !in_array($routeName, [
                'order',
                'order.complete',
            ])) {
                $return .= "\n\n" . $this->tryRender('partner-counter/_reactive');
            }

            // на всех страницах сайта, кроме...
            if (!in_array($routeName, [
                'order',
                'order.complete',
            ])) {
                $return .= "\n\n" . $this->tryRender('partner-counter/_ad4u');
            }

            // ActionPay — на странице с полным описанием продукта и на стр "спс за заказ"
            if (in_array($routeName, [
                'product',
                'order.complete',
            ])) {
                $return .= $this->tryRender('partner-counter/_actionpay', ['routeName' => $routeName] );
            }


            if ('subscribe_friends' == $routeToken) {
                $return .= $this->tryRender('partner-counter/_am15_net');
                $return .= $this->tryRender('partner-counter/_actionpay_subscribe');
                $return .= $this->tryRender('partner-counter/_cityAds_subscribe');
            }


            // ActionPay ретаргетинг
            $return .= '<div id="ActionPayJS" data-vars="' .
                $this->json( (new \View\Partners\ActionPay($routeName, $this->params))->execute() ) .
                '" class="jsanalytics"></div>';



            // вызов JS Alexa-кода
            $return .= '<div id="AlexaJS" class="jsanalytics"></div><noscript><img src="https://d5nxst8fruw4z.cloudfront.net/atrk.gif?account=mPO9i1acVE000x" style="display:none" height="1" width="1" alt="" /></noscript>';
        }

        $return .= '<div id="GoogleAnalyticsJS" class="jsanalytics"></div>';

        $return .= $this->tryRender('partner-counter/livetex/_slot_liveTex');

        return $return;
    }

    public function slotConfig() {
        return $this->tryRender('_config');
    }


    public function slotSociomantic()
    {
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
            // !!! Не дублировать! Hа этих страницах Sociomantic
            // вместе с inclusion tag
            // подключается через JS — см файл /web/js/dev/order/order.js
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
                /** @var @var $product \Model\Product\Entity */
                $category = $product->getMainCategory();
                $categories = $product->getCategory();
                if (!$category) $category = reset($categories);
                $prod_cats = $smantic->makeCategories($breadcrumbs, $category, 'product');
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

        }/* else if ($routeName == 'order.complete') {

            // !!! На этих страницах подключается через js — /web/js/dev/order/order.js

            //$products = $this->getParam('products');
            //$cartProductsById = $this->getParam('cartProductsById');
            //$cart = \App::user()->getCart();
            $orders = $this->getParam('orders'); // \Model\Order\Entity Object
            $return .= $this->render($smantic_path . '05a-confirmation_page',
                ['orders' => $orders, 'smantic' => &$smantic]
            );

            $smantic->restoreSession();

        }*/
        /*else if ( $routeName == 'order' ) {

            //$products = $this->getParam('products');
            //$smantic->makeSession( $products );

        }*/

        return !empty($return) ? $return : false;
    }

    public function slotCriteo() {
        return $this->render( 'partner-counter/_criteo',  ['criteoData' =>  (new \View\Partners\Criteo($this->params))->data()] );
    }


    public function slotRetailRocket()
    {
        $routeName = \App::request()->attributes->get('route');
        $rrObj = new \View\Partners\RetailRocket($routeName);
        $return = '';

        $rrData = null;
        if ($routeName == 'product') {

            $product = $this->getParam('product');
            $rrData = $rrObj->product($product);
        } elseif ($routeName == 'product.category') {

           $category = $this->getParam('category');
           $rrData = $rrObj->category($category);

        } elseif ($routeName == 'order.complete') {

            $orders = $this->getParam('orders');
            $rrData = $rrObj->transaction($orders);

        }
        $rrObj = null;


        $return .= '<div id="RetailRocketJS" class="jsanalytics"';
        if ($rrData) {
            $return .= ' data-value="' . $this->json($rrData) . '"';
        }
        $return .= '></div>';

        return $return;
    }




    public function slotAdmitad()
    {
        if ( \App::config()->partners['Admitad']['enabled'] ) {
            $return = '';
            $adData = [];
            $routeName = \App::request()->attributes->get('route');
            $adObj = new \View\Partners\Admitad($routeName);

            if ($routeName == 'product.category') {

                $category = $this->getParam('category');
                $adData = $adObj->category($category);

            } elseif ($routeName == 'product') {

                $product = $this->getParam('product');
                $adData = $adObj->product($product);

            } else if ($routeName == 'cart') {

                //$products = $this->getParam('products');
                $cartProductsById = $this->getParam('cartProductsById');
                $adData = $adObj->cart($cartProductsById);

            } elseif ($routeName == 'order.complete') {

                $orders = $this->getParam('orders');
                $adData = $adObj->ordercomplete($orders);

            } elseif ($routeName == 'homepage') {

                $adData = $adObj->toSend($routeName);

            }

            if (!empty($adData)) {
                $return = '<div id="AdmitadJS" data-value="' . $this->json($adData) . '" class="jsanalytics" ></div>';
            }

            return $return;
        }
        return;
    }


    public function slotEnterleads()
    {
        $routeToken = \App::request()->attributes->get('token');
        $onPages = [
            'internet_price',
            'subscribe_friends',
            'enter-friends'
        ];
        if ( !in_array($routeToken, $onPages) ) return;

        return '<div id="enterleadsJS" class="jsanalytics" ></div>';
    }


    public function slotMarinLandingPageTagJS()
    {
        return '<div id="marinLandingPageTagJS" class="jsanalytics">
            <noscript><img src="https://tracker.marinsm.com/tp?act=1&cid=7saq97byg0&script=no" ></noscript></div>';
    }


    public function slotMarinConversionTagJS()
    {
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

        $category = $this->getParam('category') instanceof \Model\Product\Category\Entity ? $this->getParam('category') : null;
        if (!$category) {
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

}
