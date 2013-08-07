<?php
namespace View\Partners;


class Criteo {

    private $params;

    function __construct($params) {
        $this->params = $params;
    }


    /**
     * Метод подготавливает переменные для head Javascript-а Criteo
     * @return array
     */
    public function data() {
        $request = \App::request();
        $routeName = $request->attributes->get('route');
        $user = \App::user();
        $userId = $user->getEntity() ? $user->getEntity()->getId() : 0;
        $cart = $user->getCart();
        $siteType = 'd';  //m for mobile or t for tablet or d for desktop // <- TODO for mobile version

        $searchQuery = (string)$request->get('q');
        $searchQuery = $this->escape($searchQuery);
        //$productCategoryRepository = \RepositoryManager::productCategory();
        //$product = \RepositoryManager::product();


        $arr_item = [];
        $eventItems_arr = [];
        $eventItemsID = [];
        $beforeItems = [];


        switch ($routeName) { // begin of case
            case "homepage":
                //$viewEvent = 'viewHome';
                //break;
                print '###! Oops. On homepage we have not data for criteo. Criteo is not running'; //tmp
                return false; // нечего передовать на homepage, выходим

            case "product":
                $viewEvent = 'viewItem';
                $eventItemsID = $this->getParam('product')->getId();
                break;

            case "cart":
                $viewEvent = 'viewBasket';
                $in_basket = $cart->getData()['productList'];
                $products = $this->getParam('productEntities');

                foreach($products as $product) {
                    /* @var $product \Model\Product\Entity */
                    $eventItems_arr[] = array(
                        'id' => $product->getId(),
                        'price' => $product->getPrice(),
                        'quantity' => $in_basket[ $product->getId() ],
                    );
                }
                break;


            case "search":
                $viewEvent = 'viewList';
                $productPager = $this->getParam('productPager'); /** @var $productPager \Iterator\EntityPager */
                $this->addProductsIDs( $productPager,  $eventItemsID );
                break;


            case "product.category":
                $productPagersByCategory = $this->getParam('productPagersByCategory');
                /* @var $productPagersByCategory \Iterator\EntityPager[] */
                $productPager = $this->getParam('productPager');
                /** @var $productPager \Iterator\EntityPager */

                $countIDs = 0;
                if ( $productPager ) {
                    $countIDs = $this->addProductsIDs( $productPager,  $eventItemsID );
                }else if ($productPagersByCategory) {
                    foreach ($productPagersByCategory as $productPager) {
                        /** @var $productPager \Iterator\EntityPager */
                        $countIDs = $this->addProductsIDs( $productPager,  $eventItemsID );
                    }
                }

                if ($countIDs) {
                    $viewEvent = 'viewList';
                }else{
                    print '###! Oops. Products array is empty on this category page. Criteo is not running'; //tmp
                    //$viewEvent = '';
                    return false; // eсли нет продуктов, нечего передовать на этой стр, выходим
                }
                unset($countIDs);

                break;

            case "order.complete":
                $viewEvent = 'trackTransaction';
                $orders = $this->getParam('orders');
                foreach($orders as $ord) {
                    /* @var $ord \Model\Order\Entity */
                    $products = $ord->getProduct();

                    foreach($products as $product) {
                        // @var $product \Model\Product\Entity
                        $eventItems_arr[]  = array(
                            'id' => $product->getId(),
                            'price' => $product->getPrice(),
                            'quantity' => $product->getQuantity(),
                        );
                    }

                }
                break;

            default:
                //$viewEvent = 'view.'.$routeName;
                //$viewEvent = '';
                print '###! Oops. On this page we have not data for criteo. Criteo is not running'; //tmp
                return false; // нечего передовать на этой стр, выходим

        }// end of case


        if (isset($orders) and !empty($orders)){
            $order = reset($orders);
            $beforeItems['id'] = $order->getNumber();
            // TODO: new_customer: 1 if first purchase or 0 if not,  deduplication: 1 if attributed to Criteo or 0 if not,
            // $beforeItems['new_customer'] = '1';
            // $beforeItems['deduplication'] = '1';
            /* // example:
            { event: "trackTransaction" , id: "Transaction Id", new_customer: 1 if first purchase or 0 if not,
                deduplication: 1 if attributed to Criteo or 0 if not, item: [
                    { id: "First item id", price: First item unit price, quantity: First item quantity }, etc
                ]
            } */
        }








        $criteoData = [
            [
                'event' => 'setAccount',
                'account' => \App::config()->partners['criteo']['account'],
            ],
            [
                'event' => 'setCustomerId',
                'id' => $userId,
            ],
            [
                'event' => 'setSiteType',
                'type' => $siteType,
            ]
        ];


        /**
         * Из $arr_item формируется массив для строки вида:
         * { event: "viewList", item: ["First item id", "Second item id", "Third item id"], keywords: "User Searched Keywords" }
         * Для всех страниц: каталога, корзины, поиска...
         */
        $arr_item['event'] = $viewEvent;
        foreach ($beforeItems as $key => $value) $arr_item[$key] = $value;

        /*
        * из $eventItemsID сформируется массив для строки вида:
        * item: ["First item id", "Second item id", "Third item id"]
        *
        * из $eventItems_arr сформируется массив для строки вида:
        * item: [ id' => ##, 'price': ##, 'quantity': ## ]
        */
        if ( !empty($eventItems_arr) ) $arr_item['item'] = $eventItems_arr;
        else if ( !empty($eventItemsID) ) $arr_item['item'] = $eventItemsID;

        if (!empty($searchQuery)) $arr_item['keywords'] = $searchQuery;

        $criteoData[] = $arr_item;




        // just for debug:
        print '<pre>';
        print_r($criteoData);
        print '</pre>';

        // just for debug:
        print '###<pre>';
        print '### PageRouteName: '.$routeName.PHP_EOL;
        //print (isset($orders) and !empty($orders)) ? print_r($orders) : "no isset orderS! \n\n";
        //print_r($criteo_q);
        //print_r($eventItems);
        //print_r($searchQuery);
        //print_r( $arr_item['keywords'] );
        print '</pre>###';


        return $criteoData ? $criteoData : false;
    }


    /**
     * @param $productPager  is array or object!
     * @param $arr
     * @return bool|int
     */
    private function addProductsIDs( &$productPager, &$arr ) {
        //if ( isset($productPager) and !empty($productPager) ) {
        if ( $productPager instanceof \Iterator\EntityPager ) {
            $i = 0;
            foreach ($productPager as $product) {
                // @var $product \Model\Product\Entity
                $arr[] = $product->getId();
                $i++;
            }
            return $i;
        }
        return false;
    }



    /**
     * @param $value
     * @return string
     */
    private function escape($value) {
        //return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        return htmlspecialchars($value, ENT_QUOTES, \App::config()->encoding);
    }


    /**
     * @param $name
     * @return null
     */
    private function getParam($name) {
        return array_key_exists($name, $this->params) ? $this->params[$name] : null;
    }

}