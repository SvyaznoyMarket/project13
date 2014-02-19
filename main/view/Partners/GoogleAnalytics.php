<?php
/**
 * Google Analytics
 *
 * User: Juljan
 * Date: 12.02.14
 * Time: 10.12
 */

namespace View\Partners;

class GoogleAnalytics {

    private $routeName;
    private $params;
    private $user;
    private $cart;

    private $sendData = [];
    //private $extraData = [];


    /**
     * @param   string    $rName
     * @param   array     $params
     */
    public function __construct($rName, $params)
    {
        $this->routeName = $rName;
        $this->params = $params;
        $this->user = \App::user();
        $this->cart = $this->user->getCart();

        $this->sendData = [];
        $this->sendData['vars'] = [];
    }


    /**
     * Возвращает массив для передачи в дата-аттрибут
     *
     * @return array $this->sendData;
     */
    public function execute()
    {
        try {
            $this->routeAll();

            switch ($this->routeName) { // begin of case
                case "homepage":
                    $this->routeHomepage();
                    break;

                case "product":
                    $this->routeProduct();
                    break;

                case "product.category":
                    $this->routeCategory();
                    break;

                case "cart":
                    $this->routeCart();
                    break;

                case "search":
                    $this->routeSearch();
                    break;

                case "order":
                    $this->routeOrderNew();
                    break;

                case "order.complete":
                    $this->routeOrderComplete();
                    break;

                default:
                    $this->routeDefault();
            }
            // end of case

        } catch (\Exception $e) {
            \App::logger()->error($e, [__CLASS__]);
        }

        if (empty($this->sendData['vars'])) {
            unset($this->sendData['vars']);
        }

        if (empty($this->sendData)) {
            $this->sendData = null;
        }
        return $this->sendData;
    }




    /**
     * Вызывается на неописанной странице
     */
    private function routeDefault() {
        $this->sendData['vars']['dimension5'] = 'Other';
    }



    /**
     * Вызывается на всех страницах счётчика
     */
    private function routeAll() {
    }



    /**
     * Вызывается на главной странице
     */
    private function routeHomepage() {
        $this->sendData['vars']['dimension5'] = 'Home';
    }



    /**
     * Вызывается на стр продукта
     */
    private function routeProduct() {
        // страница одного товара
        $this->sendData['vars']['dimension5'] = 'Item';
        $product = $this->getParam('product');
        $categories = $product->getCategory();
        $categoryUpper = reset($categories);
        $categoryDown = end($categories);
        /*
        $categoryMain = $product->getMainCategory();
        if ( !$categoryMain ) {
            $categoryMain = end( $categories );
        }*/

        if (strpos($_SERVER['HTTP_REFERER'],'search?q=') > 0) {
            $this->sendData['afterSearch'] = 1;
        }

        if ($product instanceof \Model\Product\Entity) {
            $this->sendData['vars']['dimension11'] = $product->getArticle();

            $this->sendData['vars']['dimension10'] =
                ($product->isInShopOnly() || $product->isInShopStockOnly())
                    ? 'NO'
                    : 'YES';
        }

        $this->sendData['vars']['dimension6'] = $categoryUpper->getName();  // Имя верхней категории
        $this->sendData['vars']['dimension12'] = $categoryDown->getName();  // Имя текущей подкатегории
        $this->sendData['upperCat'] = $categoryUpper->getName();            // Имя верхней категории
    }


    /**
     * Добавляет инфу о категории и её родительской категори, если оная существует.
     *
     * @param $category
     */
    /*private function addCategoryInfo($category) {
        if ( $category instanceof \Model\Product\Category\Entity ) {
            $this->sendData['vars']['dimension12'] = $category->getName(); // Имя текущей категории

            $parentCat = $category->getParent();
            if ($parentCat) {
                $this->sendData['vars']['dimension6'] = $parentCat->getName();  // Имя родительской категории, если есть
            } else {
                $this->sendData['vars']['dimension12'] = $category->getName(); // Если нет родительской
            }
        }
    }*/


    /**
     * Вызывается на стр категории
     */
    private function routeCategory() {
        // страница каталога/категории/подкатегории
        $this->sendData['vars']['dimension5'] = 'Category';
        $category = $this->getParam('category');
        if ($category instanceof \Model\Product\Category\Entity) {
            $categories = $category->getAncestor();
            $categoryUpper = reset($categories);
            $categoryDown = end($categories);
            $this->sendData['vars']['dimension6'] = $categoryUpper->getName();  // Имя верхней категории
            $this->sendData['vars']['dimension12'] = $categoryDown->getName();  // Имя текущей подкатегории
        }
    }



    /**
     * Вызывается на стр поиска
     */
    private function routeSearch() {
        $this->sendData['vars']['dimension5'] = 'Search';
    }



    /**
     * Вызывается на стр корзины
     */
    private function routeCart() {
        // корзина
        $this->sendData['vars']['dimension5'] = 'Cart';

        $total = 0;
        $SKUs = '';
        $userEntity = \App::user()->getEntity();
        $cartProductsById = $this->getParam('cartProductsById');
        $products = $this->getParam('products');
        //$cartServicesById = $this->getParam('cartServicesById');

        if (!$cartProductsById /*&& !$cartServicesById*/) return false;

        foreach ($products as $product) {
            if (!isset($cartProductsById[$product->getId()])) continue;
            //$cartProduct = $cartProductsById[$product->getId()];
            /** @var $cartProduct \Model\Cart\Product\Entity */
            /** @var $product \Model\Product\CartEntity */
            $SKUs .= $product->getArticle() . ',';
            $total += $product->getPrice();
        }

        /*foreach($cartServicesById as $item) {
            // @var $product \Model\Order\Service\Entity
            $total += $item->getSum();
        }*/

        self::rmLastSeporator($SKUs);

        $this->sendData['cart'] = [
            'sum'   => $total,
            'SKUs'  => $SKUs,
            'uid'   => $userEntity ? $userEntity->getId() : 0,
        ];
    }


    private function routeOrderNew() {
        $this->sendData['vars']['dimension5'] = 'Checkout';
    }


    /**
     * Вызывается на страницАХ заказа
     */
    private function routeOrder() {
        // оформление заказа

        $orders = $this->getParam('orders');
        if (!$orders) return false;
        $orderNumbersArr = [];

        $orderSum = 0;
        foreach ($orders as $order) {
            $orderNumbersArr[] = $order->getNumber();
            $orderSum += $order->getPaySum();
        }

        $orderInfo = [
          //'id'        =>  reset($orderNumbers),       // Берём номер первого заказа
            'id'        => implode(", ", $orderNumbersArr), // Берём все номера заказов через запятую
            'price'     => $orderSum,
        ];

        if ( empty($orderInfo) ) return false;
        $this->sendData['orderInfo'] = $orderInfo;
    }


    private function route404() {
        $this->sendData['vars']['dimension5'] = '404';
    }

    /**
     * Вызывается на страницЕ "Спасибо за заказ"
     */
    private function routeOrderComplete() {
        if ($this->getParam('sessionIsReaded')) {
            // Если этот параметр существует и тру, то юзер обновляет страницу, не нужно трекать заново
            return false;
        }

        $this->sendData['vars']['dimension5'] = 'ThankYou';

        $productsById = $this->getParam('productsById');
        $ordersAll = $this->getParam('orders');
        if (!$ordersAll) return false;
        /** @var $orders \Model\Order\Entity **/


        $this->sendData['ecommerce'] = [];
        $purchasedProducts = []; //купленные товары
        $addTransaction = [
            'id'        => '', // Transaction ID. Required.
            'revenue'   => 0, // Grand Total.
            'shipping'  => 0, // Shipping.
        ];


        foreach($ordersAll as $order) {
            /** @var $order \Model\Order\Entity **/
            $products = $order->getProduct();
            $delivery = $order->getDelivery();
            $delivery = reset($delivery);

            $addTransaction['id'] .= $order->getId() . ',';
            $addTransaction['revenue'] += $order->getSum();
            if ( $delivery instanceof \Model\Order\Delivery\Entity ) {
                $addTransaction['shipping'] += $delivery->getPrice();
            }

            foreach($products as $orderProduct) {
                /** @var $orderProduct  \Model\Order\Product\Entity **/
                /** @var $product       \Model\Product\Entity       **/

                $product = isset($productsById[$orderProduct->getId()]) ? $productsById[$orderProduct->getId()] : false;

                $purchasedProducts[] = [
                    'id'        => $orderProduct->getId(),
                    //'name'      => $orderProduct->getName(), // нет такого метода
                    'name'      => $product ? $product->getName() : '',
                    'price'     => $orderProduct->getPrice(),
                    'quantity'  => $orderProduct->getQuantity(),
                ];
            }

        }

        self::rmLastSeporator($addTransaction['id']);

        $this->sendData['ecommerce']['addTransaction'] = $addTransaction;
        $this->sendData['ecommerce']['items'] = $purchasedProducts;
    }


    private function rmLastSeporator(&$str) {
        $str = (string) $str;
        if (isset($str[1])) {
            $str = substr($str, 0, strlen($str)-1);
        }
        return $str;
    }


    /**
     * @param $name
     * @return null
     */
    private function getParam($name) {
        return array_key_exists($name, $this->params) ? $this->params[$name] : null;
    }


}
