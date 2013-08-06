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
                //$eventItems = $this->getParam('product')->getId();
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
                foreach($orders as $ord) {
                    /* @var $ord \Model\Order\Entity */
                    $products = $ord->getProduct();
                    foreach($products as $product) {
                        $eventItems_arr[] = '{ id: "'.$product->getId().
                            '", price: '.$product->getPrice().
                            ', quantity: '.$product->getQuantity().' }';
                    }
                }
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
        //print '<pre>!!!';
        //print_r($criteoData);
        //print '</pre>';

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