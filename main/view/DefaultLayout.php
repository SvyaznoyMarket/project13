<?php

namespace View;

class DefaultLayout extends Layout {
    protected $layout  = 'layout-twoColumn';

    public function __construct() {
        parent::__construct();

        $this->setTitle('Enter - это выход!');
        $this->addMeta('yandex-verification', '623bb356993d4993');
        $this->addMeta('viewport', 'width=900');
        $this->addMeta('title', 'Enter - это выход!');
        $this->addMeta('description', 'Enter - новый способ покупать. Любой из ' . \App::config()->product['totalCount'] . ' товаров нашего ассортимента можно купить где угодно, как угодно и когда угодно. Наша миссия: дарить время для настоящего. Честно. С любовью. Как для себя.');

        // TODO: осторожно, говнокод
        if ('live' != \App::$env) {
            $this->addMeta('apple-itunes-app', 'app-id=486318342,affiliate-data=, app-argument=');
        }
        /* Meta and Title могут быть переопределены в методе prepare() в /main/controller/Main/IndexAction.php
            — загружаются там из json для главной страницы, например.
        */

        $this->addStylesheet('/css/global.min.css');

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

    public function slotMobileModify() {
        if (\App::config()->mobileModify['enabled']) {
            return $this->tryRender('_mobileModify');
        }

        return '';
    }

    public function slotGoogleAnalytics() {
        return $this->tryRender('_googleAnalytics');
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

        try {
            $response = $client->query('footer_default');
        } catch (\Exception $e) {
            \App::exception()->add($e);
            \App::logger()->error($e);

            $response = array('content' => '');
        }

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
        return $this->render('_userbar');
    }

    public function slotSurveybar() {
        $cookieInitTimeStamp = (int)(\App::request()->cookies->get('survey'));
        $survey = \RepositoryManager::survey()->getEntity();
        if (!$survey) {
            return '';
        }
        $region = \App::user()->getRegion();

        if(!$survey) {
            return '';
        }

        $regionsToShow = array_intersect([$region->getName(), 'все', 'Все', 'all'], $survey->getRegionNames());
        $showSurvey = $survey->getIsActive() && !empty($regionsToShow) && !$survey->isAnswered($cookieInitTimeStamp);
        return $showSurvey ? $this->render('_surveybar', ['survey' => $survey]) : '';
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
            });
            $client->execute();

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

            \App::debug()->add('time.main-menu', sprintf('%s ms', round(\Debug\Timer::get('main-menu')['total'], 3) * 1000), 95);
        }

        return $content;
    }

    public function slotBanner() {
        return '';
    }

    public function slotPartnerCounter() {
        $return = '';

        if (\App::config()->analytics['enabled']) {
            $routeName = \App::request()->attributes->get('route');

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

        }

        return $return;
    }

    public function slotConfig() {
        return $this->tryRender('_config');
    }


    public function slotSociomantic()
    {
        $smantic_path = 'partner-counter/sociomantic/';
        $routeName = \App::request()->attributes->get('route');
        $breadcrumbs = $this->getParam('breadcrumbs');
        $region_id = \App::user()->getRegion()->getId();
        $smantic = new \View\Partners\Sociomantic($region_id);

        // на всех страницах сайта // необходимо установить наш код главной страницы (inclusion tag)
        $return = $this->render($smantic_path . '01-homepage');


        /*
        if (!in_array($routeName, [
            // на этих страницах Sociomantic подключается через JS
            'order',
            'order.complete',
        ])) // orders-if-begin{
        */

        if ($routeName == 'product.category') {

            $category = $this->getParam('category') instanceof \Model\Product\Category\Entity ? $this->getParam('category') : null;
            $prod_cats = $smantic->makeCategories($breadcrumbs, $category, 'category');
            $return .= $this->render($smantic_path . '02-category_page', ['category' => $category, 'smantic' => &$smantic, 'prod_cats' => $prod_cats]);

        } else if ($routeName == 'product') {

            $product = $this->getParam('product') instanceof \Model\Product\Entity ? $this->getParam('product') : null;
            $prod_cats = $smantic->makeCategories($breadcrumbs, $product->getCategory(), 'product');
            $return .= $this->render($smantic_path . '03a-product_page_stream', ['product' => $product, 'smantic' => &$smantic, 'prod_cats' => $prod_cats]);

        } else if ($routeName == 'cart') {

            $products = $this->getParam('products');
            $cartProductsById = $this->getParam('cartProductsById');
            $cart_prods = $smantic->makeCartProducts($products, $cartProductsById);
            $return .= $this->render($smantic_path . '04-basket', ['cart_prods' => $cart_prods, 'smantic' => &$smantic]);

        } else if ($routeName == 'order.complete') {

            //$products = $this->getParam('products');
            //$cartProductsById = $this->getParam('cartProductsById');
            //$cart = \App::user()->getCart();
            $orders = $this->getParam('orders'); // \Model\Order\Entity Object
            $return .= $this->render($smantic_path . '05a-confirmation_page',
                ['orders' => $orders, 'smantic' => &$smantic]
            );

            $smantic->restoreSession();

        }
        /*else if ( $routeName == 'order' ) {

            //$products = $this->getParam('products');
            //$smantic->makeSession( $products );

        }*/

        return isset($return) ? $return : false;
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


}
