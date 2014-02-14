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
        $category = $product->getMainCategory();

        if ( !$category ) {
            $categories = $product->getCategory();
            $category = reset( $categories );
        }

        if ($product instanceof \Model\Product\Entity) {
            $this->sendData['vars']['dimension11'] = $product->getArticle();

            $this->sendData['vars']['dimension10'] =
                ($product->isInShopOnly() || $product->isInShopStockOnly())
                    ? 'NO'
                    : 'YES';
        }

        $this->addCategoryInfo($category);
    }


    /**
     * Добавляет инфу о категории и её родительской категори, если оная существует.
     * Вызывается на стр. товара и стр. категории.
     *
     * @param $category
     */
    private function addCategoryInfo($category) {
        if ( $category instanceof \Model\Product\Category\Entity ) {
            $this->sendData['vars']['dimension12'] = $category->getName(); // Имя текущей категории

            $parentCat = $category->getParent();
            if ($parentCat) {
                $this->sendData['vars']['dimension6'] = $parentCat->getName();  // Имя родительской категории, если есть
            } else {
                $this->sendData['vars']['dimension12'] = $category->getName(); // Если нет родительской
            }
        }
    }


    /**
     * Вызывается на стр категории
     */
    private function routeCategory() {
        // страница каталога/категории/подкатегории
        $this->sendData['vars']['dimension5'] = 'Category';
        $this->addCategoryInfo($this->getParam('category'));
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
        $this->sendData['vars']['dimension5'] = 'Search';

        $total = 0;
        $cartProductsById = $this->getParam('cartProductsById');
        $cartServicesById = $this->getParam('cartServicesById');

        foreach($cartProductsById as $item) {
            /** @var $item \Model\Order\Product\Entity */
            $total += $item->getSum();
        }

        foreach($cartServicesById as $item) {
            /** @var $product \Model\Order\Service\Entity */
            $total += $item->getSum();
        }

        $this->sendData['cart'] = [
            'totalSum'  => $total,
            'SKUs'      => '',
            'KissUIDs'  => '',
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
        $this->routeOrder();

        $this->sendData['vars']['dimension5'] = 'ThankYou';

        // последняя страница оформление заказа
        //$this->sendData['pageType'] = 6;

        $orders = $this->getParam('orders');
        if (!$orders) return false;
        /** @var $orders \Model\Order\Entity **/

        $productsById = $this->getParam('productsById');

        //$orders->getSum()

        //купленные товары
        $purchasedProducts = [];

        foreach($orders as $ord) {
            /** @var $ord \Model\Order\Entity **/
            $products = $ord->getProduct();

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

        if (empty($purchasedProducts)) return false;

        $this->sendData['purchasedProducts'] = $purchasedProducts;
    }





    /**
     * @param $name
     * @return null
     */
    private function getParam($name) {
        return array_key_exists($name, $this->params) ? $this->params[$name] : null;
    }


    /**
     * For debug
     *
     * @param null $var
     * @param string $info
     *
    private function d($var = null, $info = '') {
        print '<pre>Debug:';
        if ($info) {
            print '###';
            print_r($info);
            print ' - - - - ';
        }
        if ($var) print_r($var);
        print '</pre>';
    }*/

}