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

        $this->addJavascript('/js/loadjs.js');
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
            'http://yandex.st/jquery/1.6.4/jquery.min.js',
            '/js/prod/LAB.min.js',
        ] as $javascript) {
            $return .= '<script src="' . $javascript . '" type="text/javascript"></script>' . "\n";
        }

        $return .= $this->render('_headJavascript');

        // TODO не передовать все параметры $this->params
        $criteo_vars_arr = $this->params + [ 'criteo_q' => $this->vars4jsCriteo() ];
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
                $viewEvent = 'viewHome';
                break;

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

                foreach ($productPager as $product) {
                    // @var $product \Model\Product\Entity
                    $eventItems_arr[] = '"'.$product->getId().'"';
                }
                break;


            case "product.category":
                $viewEvent = 'viewList';

                $productPagersByCategory = $this->getParam('productPagersByCategory');

                if ($productPagersByCategory)
                foreach ($productPagersByCategory as $productPager) {
                    foreach ($productPager as $product) {
                        // @var $product \Model\Product\Entity
                        $eventItems_arr[] = '"'.$product->getId().'"';
                    }
                }

                break;

            case "order.complete":
                $viewEvent = 'trackTransaction';
                $orders = $this->getParam('orders');
                break;

            default:
                $viewEvent = 'view.'.$routeName;

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


        $criteo_q = [];

        $criteo_q[] = [
            'event' => 'setAccount',
            'account' => \App::config()->partners['criteo']['account'],
        ];

        $criteo_q[] = [
            'event' => 'setCustomerId',
            'id' => $userId,
        ];

        $criteo_q[] = [
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

        $criteo_q[] = $arr_item;



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


        return $criteo_q ? $criteo_q : false;
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
}
