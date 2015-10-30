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
    public function execute() {
        $criteoData = [];
        $arr_item = [];
        $eventItems_arr = [];
        $eventItemsID = [];
        $beforeItems = [];

        try {

            $request = \App::request();
            $routeName = $request->attributes->get('route');
            $user = \App::user();
            $userId = $user->getEntity() ? $user->getEntity()->getId() : 0;
            $cart = $user->getCart();
            $siteType = 'd';  //m for mobile or t for tablet or d for desktop // <- TODO for mobile version

            $searchQuery = (string)$request->get('q');
            $searchQuery = $this->escape($searchQuery);

            switch ($routeName) {
                case 'product':
                    $viewEvent = 'viewItem';
                    $eventItemsID = $this->getParam('product')->getId();
                    break;

                case 'cart':
                    $viewEvent = 'viewBasket';
                    //$in_basket = $cart->getData()['productList'];
                    $in_basket = $cart->getProductsById();
                    $products = $this->getParam('products');

                    foreach($products as $product) {
                        /* @var $product \Model\Product\Entity */
                        $eventItems_arr[] = [
                            'id' => $product->getId(),
                            'price' => $product->getPrice(),
                            'quantity' => $in_basket[$product->getId()]->quantity,
                        ];
                    }
                    break;

                case 'search':
                case 'product.category':
                case 'slice.show':
                case 'slice.category':
                case 'product.set':
                case 'sale.all':
                case 'sale.one':
                case 'wishlist.show':
                    /** @var \Iterator\EntityPager $productPager */
                    $productPager = $this->getParam('productPager');

                    $countIDs = 0;
                    if ($productPager) {
                        $countIDs = $this->addProductsIDs( $productPager,  $eventItemsID );
                    }

                    if ($countIDs) {
                        $viewEvent = 'viewList';
                    } else {
                        //$viewEvent = '';
                        return false; // eсли нет продуктов, нечего передовать на этой стр, выходим
                    }
                    unset($countIDs);

                    break;

                case 'order.complete':
                case 'orderV3.complete':
                case 'orderV3OneClick.create':
                    $viewEvent = 'trackTransaction';
                    $orders = $this->getParam('orders');
                    foreach($orders as $ord) {
                        /* @var $ord \Model\Order\Entity */
                        $products = $ord->getProduct();

                        foreach($products as $product) {
                            // @var $product \Model\Product\Entity
                            $eventItems_arr[]  = [
                                'id' => $product->getId(),
                                'price' => $product->getPrice(),
                                'quantity' => $product->getQuantity(),
                            ];
                        }

                    }
                    break;

                default:
                    $viewEvent = 'viewHome';
                    break;
            }


            if (isset($orders) and !empty($orders)){
                $order = reset($orders);
                $beforeItems['id'] = $order->getNumber();
                // $beforeItems['new_customer'] = '1'; // TODO: new_customer: 1 if first purchase or 0 if not,  deduplication: 1 if attributed to Criteo or 0 if not,
                $beforeItems['deduplication'] = (int) $this->utmzCookieHas('criteo');
                /* // example:
                {
                    event: 'trackTransaction' ,
                    id: 'Transaction Id',
                    new_customer: 1 if first purchase or 0 if not,
                    deduplication: 1 if attributed to Criteo or 0 if not,
                    item: [
                        {
                            id: 'First item id',
                            price: First item unit price,
                            quantity: First item quantity
                        }, etc ...
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

        } catch (\Exception $e) {
            \App::logger()->error($e, [__CLASS__]);
            \App::exception()->remove($e);
        }

        return $criteoData;
    }


    /**
     * @param \Iterator\EntityPager $productPager
     * @param $arr
     * @return bool|int
     */
    private function addProductsIDs( $productPager, &$arr ) {
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


    /**
     * @param   string $paramName
     * @return  bool
     */
    private function utmzCookieHas($paramName) {
        $cookValue = \App::request()->cookies->get('__utmz');
        if ((bool)$cookValue && false !== strpos($cookValue, $paramName)) {
            return true;
        }
        return false;
    }

}