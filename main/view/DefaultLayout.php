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
            'http://yandex.st/jquery/1.8.3/jquery.min.js',
            '/js/prod/LAB.min.js',
        ] as $javascript) {
            $return .= '<script src="' . $javascript . '" type="text/javascript"></script>' . "\n";
        }

        $return .= $this->render('_headJavascript');

        $criteo_vars_arr = [
            'criteoData' =>  $this->vars4jsCriteo()
        ];
        $return .= $this->render('partner-counter/_criteo',  $criteo_vars_arr);

        return $return;
    }


    /**
     * Метод подготавливает переменные для head Javascript-а Criteo
     * @return array
     */
    private function vars4jsCriteo(){

        $request = \App::request();
        $user = \App::user();

        $userId = $user->getEntity() ? $user->getEntity()->getId() : 0;
        $cart = $user->getCart();

        $routeName = $request->attributes->get('route');
        $siteType = 'd';  //m for mobile or t for tablet or d for desktop


        $searchQuery = (string)$request->get('q');
        $searchQuery = $this->escape($searchQuery);

        //$productCategoryRepository = \RepositoryManager::productCategory();
        //$product = \RepositoryManager::product();


        $arr_item = [];
        $eventItems_arr = [];
        $eventItems = (string)'';
        $beforeItems = [];


        switch ($routeName) { // begin of case
            case "homepage":
                //$viewEvent = 'viewHome';
                //break;
                return false; // нечего передовать на homepage, выходим

            case "product":
                $viewEvent = 'viewItem';
                $eventItems = $this->getParam('product')->getId();
                break;


            case "cart":
                $viewEvent = 'viewBasket';
                $in_basket = $cart->getData()['productList'];
                $products = $this->getParam('productEntities');

                foreach($products as $product) {
                    /* @var $product \Model\Product\Entity */

                    /* // работает, но многобукаф и проверок
                    $arr_item['id'] = $product->getId();
                    $arr_item['price'] = $product->getPrice();
                    $arr_item['quantity'] = $in_basket[ $arr_item['id'] ];

                    $properts = [];
                    foreach($arr_item as $key => $val) {
                        $properts[] = $this->helper->stringRowParam4js($key, $val);
                    }

                    if ( !empty($properts) )
                        $eventItems_arr[] = '{'.implode('; ',$properts).'}';
                    */

                    $eventItems_arr[] = '{ id: "'.$product->getId().
                                        '", price: '.$product->getPrice().
                                        ', quantity: '.$in_basket[ $product->getId() ].' }'; // так проще

                }

                break;


            case "search":
                $viewEvent = 'viewList';
                $productPager = $this->getParam('productPager');

                if ($productPager instanceof \Iterator\EntityPager) {
                    foreach ($productPager as $product) {
                        // @var $product \Model\Product\Entity
                        $eventItems_arr[] = '"'.$product->getId().'"';
                    }
                    break;
                }

            case "product.category":
                $productPagersByCategory = $this->getParam('productPagersByCategory');

                if ($productPagersByCategory)
                foreach ($productPagersByCategory as $productPager) {
                    foreach ($productPager as $product) {
                        // @var $product \Model\Product\Entity
                        $eventItems_arr[] = '"'.$product->getId().'"';
                    }
                }

                if (!empty($eventItems_arr)) {
                    $viewEvent = 'viewList';
                }else{
                    //$viewEvent = '';
                    return false; // eсли нет продуктов, нечего передовать на этой стр, выходим
                }

                break;

            case "order.complete":
                $viewEvent = 'trackTransaction';
                $orders = $this->getParam('orders');
                break;

            default:
                //$viewEvent = 'view.'.$routeName;
                //$viewEvent = '';
                return false; // нечего передовать на этой стр, выходим

        }// end of case



        if (isset($orders) and !empty($orders)){
            $order = $orders[0];
            $beforeItems['id'] = $order->getNumber();
            // TODO: new_customer: 1 if first purchase or 0 if not,  deduplication: 1 if attributed to Criteo or 0 if not,
            // $beforeItems['new_customer'] = '1';
            // $beforeItems['deduplication'] = '1';
            /*
            // example:
            { event: "trackTransaction" , id: "Transaction Id", new_customer: 1 if first purchase or 0 if not,
                deduplication: 1 if attributed to Criteo or 0 if not, item: [
                    { id: "First item id", price: First item unit price, quantity: First item quantity }, etc
                ]
            }
            */
        }


        if (empty($eventItems))
            if (!empty($eventItems_arr)) {
                /*
                 * из $eventItems (или $eventItems_arr) сформируется строка вида:
                 * item: ["First item id", "Second item id", "Third item id"]
                 */
                if (is_array($eventItems_arr))
                    $eventItems = (string)'[' . implode(', ', $eventItems_arr) . ']';

            }


        $criteoData = [];

        $criteoData[] = [
            'event' => 'setAccount',
            'account' => \App::config()->partners['criteo']['account'],
        ];

        $criteoData[] = [
            'event' => 'setCustomerId',
            'id' => $userId,
        ];

        $criteoData[] = [
            'event' => 'setSiteType',
            'type' => $siteType,
        ];


        /**
         * Из $arr_item формируется строка вида:
         * { event: "viewList", item: ["First item id", "Second item id", "Third item id"], keywords: "User Searched Keywords" }
         * Для всех страниц: каталога, корзины, поиска...
         */
        $arr_item['event'] = $viewEvent;
        foreach ($beforeItems as $key => $value) $arr_item[$key] = $value;
        if (!empty($eventItems)) $arr_item['item'] = (string)$eventItems;
        if (!empty($searchQuery)) $arr_item['keywords'] = $searchQuery;

        $criteoData[] = $arr_item;



        // just for debug:
        /*
        print '###<pre>';
        print '$$$ routeName: '.$routeName.PHP_EOL;
        //print (isset($orders) and !empty($orders)) ? print_r($orders) : "no isset orderS! \n\n";
        //print_r($criteo_q);
        //print_r($eventItems);
        //print_r($searchQuery);
        //print_r( $arr_item['keywords'] );
        print '</pre>###';
        */


        return $criteoData ? $criteoData : false;
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
            $client->execute(\App::config()->coreV2['retryTimeout']['short']);

            if ($isFailed) {
                $content = $renderer->render('__mainMenu', [
                    'menu'            => (new Menu())->generate(),
                    'catalogJsonBulk' => $catalogJsonBulk,
                    'promoHtmlBulk' => $promoHtmlBulk,
                ]);
            }
        } else {

            \Debug\Timer::start('main-menu');
            $content = $renderer->render('__mainMenu', [
                'menu'            => (new Menu())->generate(),
                'catalogJsonBulk' => $catalogJsonBulk,
                'promoHtmlBulk' => $promoHtmlBulk,
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
                'order.create',
                'order.complete',
                'cart',
            ])) {
                $return .= "\n\n" . $this->tryRender('partner-counter/_cityads');
            }

            // на всех страницах сайта, кроме shop.*
            if ((0 !== strpos($routeName, 'shop')) && !in_array($routeName, [
                'order.create',
                'order.complete',
            ])) {
                $return .= "\n\n" . $this->tryRender('partner-counter/_reactive');
            }

            // на всех страницах сайта, кроме...
            if (!in_array($routeName, [
                'order.create',
                'order.complete',
            ])) {
                $return .= "\n\n" . $this->tryRender('partner-counter/_ad4u');
            }
        }

        return $return;
    }

    public function slotConfig() {
        return $this->tryRender('_config');
    }


    public function slotSociomantic() {
        $smantic_path = 'partner-counter/sociomantic/';
        $routeName = \App::request()->attributes->get('route');
        //$routeName = $request->attributes->get('route');

        $return = "<!-- $routeName - routename -->"; //tmp
        $return .= $this->render($smantic_path.'01-homepage'); // default, для всех страниц


        if ($routeName == 'product.category') {

            $category = $this->getParam('category') instanceof \Model\Product\Category\Entity ? $this->getParam('category') : null;
            $prod_cats = $this->prod_cats_in_string( $category->getChild() ); // формируем категории продукта в виде строки для js
            $return .= $this->render($smantic_path.'02-category_page', ['category' => $category, 'prod_cats' => $prod_cats] );

        }else if ($routeName == 'product') {

            $product = $this->getParam('product') instanceof \Model\Product\Entity ? $this->getParam('product') : null;
            $prod_cats = $this->prod_cats_in_string( $product->getCategory() );  // категории продукта в виде строки для js
            $return .= $this->render($smantic_path.'03a-product_page_stream', ['product' => $product, 'prod_cats' => $prod_cats] );

        }
        else if ($routeName == 'cart') {
            $products = $this->getParam('products');
            $cartProductsById = $this->getParam('cartProductsById');

            $region_id = \App::user()->getRegion()->getId();

            $cart_prods = [];

            foreach ($products as $product):
                $cartProduct = isset($cartProductsById[$product->getId()]) ? $cartProductsById[$product->getId()] : null;

                $one_prod = [];

                $one_prod['identifier'] = (string) $product->getId();
                if ( $product->getTypeId() ) $one_prod['identifier'] .= '-'.$product->getTypeId();
                if ( $region_id ) $one_prod['identifier'] .= '_'.$region_id;

                $one_prod['quantity'] = $cartProduct->getQuantity();
                //$one_prod['amount'] = $this->helper->formatPrice( $cartProduct->getPrice() * $one_prod['quantity'] );
                $one_prod['amount'] = $cartProduct->getPrice() * $one_prod['quantity'];
                $one_prod['currency'] = 'RUB';

                $cart_prods[] = $one_prod;
                if (!$cartProduct) continue;
            endforeach;

            $return .= $this->render($smantic_path.'04-basket', ['cart_prods' => $cart_prods] );

        }
        else if ($routeName == 'order.complete') {

            //$products = $this->getParam('products');
            //$cartProductsById = $this->getParam('cartProductsById');

            $orders = $this->getParam('orders');

            //$cart = \App::user()->getCart();

            $return .= $this->render($smantic_path.'05a-confirmation_page',
                [
                    'orders' => $orders
                ]
            );

        }

        return $return;
    }


    /**
     * Возвращает категории продукта в виде строки (для js-скрипта например) исходя из масива
     * @param $prod_cats_arr
     * @return string|bool
     */
    public function prod_cats_in_string($prod_cats_arr){
        if (empty($prod_cats_arr)) return false;

        $count = count($prod_cats_arr);
        $prod_cats = '';

        if ($count > 0) {
            $i = 0;
            $prod_cats = "[";

            foreach ($prod_cats_arr as $cat) {
                $i++;
                $prod_cats .= " '" . $cat->getName() . "'";
                if ($i < $count) $prod_cats .= ", ";
            }

            $prod_cats .= " ]";
        }

        return $prod_cats;

    }

}
