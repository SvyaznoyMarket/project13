<?php
/**
 * ActionPay ретаргетинг
 *
 * User: Juljan
 * Date: 22.10.13
 * Time: 9.49
 */

namespace View\Partners;

class ActionPay {

    private $routeName;
    private $params;
    private $user;
    private $cart;

    private $sendData = [];

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

        $this->sendData['pageType'] = 0;
    }


    /**
     * Возвращает массив для передачи в ActionPayJS
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
                    $this->routeBasket();
                    break;

                case "search":
                    $this->routeSearch();
                    break;

                case "order":
                case "orderV3":
                case "orderV3.delivery":
                    $this->routeOrderNew();
                    break;

                case "orderV3.complete":
                    $this->routeOrderComplete();
                    break;

                default:
                    $this->routeDefault();
            }
            // end of case
        } catch (\Exception $e) {
            \App::logger()->error($e, ['ActionPayJS']);
        }

        return $this->sendData;
    }



    /**
     * Добавляет в массив информацию о содержимом корзине
     *
     * @return bool
     */
    private function basketInfo() {
        $this->sendData['basketProducts'] = [];
        foreach ((array)$this->cart->getProductData() as $product) {
            $this->sendData['basketProducts'][] = array(
                'id' => @$product['id'],
                'price' => @$product['price'],
                'quantity' => @$product['quantity'],
            );
        }
    }



    /**
     * Вызывается на неописанной странице
     */
    private function routeDefault() {}



    /**
     * Вызывается на всех страницах счётчика
     */
    private function routeAll() {

        /**
         * Рекомендуется передавать basketProducts на всех типах страниц,
         * а не только при просмотре корзины.
         */
        $this->basketInfo();
    }



    /**
     * Вызывается на главной странице
     */
    private function routeHomepage() {
        // главная страница сайта
        $this->sendData['pageType'] = 1;
    }



    /**
     * Вызывается на стр продукта
     */
    private function routeProduct() {
        // страница одного товара
        $this->sendData['pageType'] = 2;

        /** @var $product \Model\Product\Entity */
        $product = $this->getParam('product');

        /* @var $category \Model\Product\Category\Entity */
        $category = $product->getMainCategory();

        if ( !$category ) {
            $categories = $product->getCategory();
            $category = reset( $categories );
        }

        $this->sendData['currentProduct'] = [
            'id'    => $product->getId(),
            'name'  => $product->getName(),
            'price' => $product->getPrice()
        ];

        $this->categoryInfo($this->sendData['currentCategory'], $category);
        $this->checkParentCategory($category);
    }


    /**
     * Вызывается на стр категории
     */
    private function routeCategory() {
        // страница каталога/категории/подкатегории
        $this->sendData['pageType'] = 3;
        $category = $this->getParam('category');
        $this->categoryInfo($this->sendData['currentCategory'], $category);
        $this->checkParentCategory($category);
    }



    /**
     * Вызывается на стр поиска
     */
    private function routeSearch() {
        //$this->routeCategory(); // Страница поиска не относится к страницам каталога
    }



    /**
     * Вызывается на стр корзины
     */
    private function routeBasket() {
        $this->routeOrder();

        // корзина
        $this->sendData['pageType'] = 4;

        // $this->basketInfo(); // вызывается на всех страницах, не только на стр корзины
    }


    private function routeOrderNew() {
        // оформление заказа
        $this->sendData['pageType'] = 5;
    }

    /**
     * Вызывается на страницАХ заказа
     */
    private function routeOrder() {
        // оформление заказа
        $this->sendData['pageType'] = 5;

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
            'totalPrice'     => $orderSum,
        ];

        if ( empty($orderInfo) ) return false;
        $this->sendData['orderInfo'] = $orderInfo;
    }



    /**
     * Вызывается на страницЕ "Спасибо за заказ"
     */
    private function routeOrderComplete() {
        $this->routeOrder();

        // последняя страница оформление заказа
        $this->sendData['pageType'] = 6;

        $orders = $this->getParam('orders');
        if (!$orders) return false;
        /** @var $orders \Model\Order\Entity **/

        $productsById = $this->getParam('products');

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
     * Добавляет в переменную &$var нужные поля из $category
     *
     * @param   $var
     * @param   $category
     * @return  bool
     */
    private function categoryInfo(&$var, $category) {
        if ( !($category) ) return false;
        //if ( !($category instanceof \Model\Product\Category\Entity) ) return false;

        /** @var @var $category \Model\Product\Category\Entity */

        $catInfo = [
            'id'    =>  $category->getId(),
            'name'  =>  $category->getName(),
        ];

        if ($catInfo) {
            $var = $catInfo;
            return true;
        }

        return false;
    }


    /**
     * Проверяет родительскую категорию и добавляет в массив, если она есть
     * Пока проверяем только 1 родительский уровень
     *
     * @param   \Model\Product\Category\Entity  $category
     * @return bool
     */
    private function checkParentCategory($category)
    {
        $parentCat = null;
        $this->categoryInfo($parentCat, $category ? $category->getParent() : null);
        if ( empty($parentCat) ) return false;

        if ( !isset($this->sendData['parentCategories']) ) $this->sendData['parentCategories'] = [];
        $this->sendData['parentCategories'][] = $parentCat;
        return true;
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