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
        $this->routeAll();

        print '<pre>debug ### ';
        print_r($this->routeName);
        print '</pre>';

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

            case "order.complete":
                $this->routeOrderComplete();
                break;

            default:
                $this->routeDefault();
        }
        // end of case


        return $this->sendData;
    }



    private function routeDefault() {}


    /**
     * Вызывается на всех страницах счётчика
     */
    private function routeAll() {

        /**
         * Рекомендуется передавать basketProducts на всех типах страниц,
         * а не только при просмотре корзины.
         */
        $in_basket = $this->cart->getData()['productList'];
        $products = $this->getParam('productEntities');

        if (!$products) return false;
        $basketProd = &$this->sendData['basketProducts'];

        foreach($products as $product) {
            /* @var $product \Model\Product\Entity */
            $basketProd[] = array(
                'id' => $product->getId(),
                'price' => $product->getPrice(),
                'quantity' => $in_basket[ $product->getId() ],
            );
        }

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

        $this->sendData['currentProduct'] = $product->getId();
        $this->sendData['currentCategory'] = $category->getId();

        //$this->d( $this->sendData );
    }


    /**
     * Вызывается на стр категории
     */
    private function routeCategory() {
        // страница каталога/категории/подкатегории
        $this->sendData['pageType'] = 3;

        $category = $this->getParam('category');

        if ($category) {
            /** @var $category \Model\Product\Category\Entity */
            $this->sendData['currentCategory'] = $category->getId();
        }

    }


    /**
     * Вызывается на стр поиска
     */
    private function routeSearch() {
        $this->routeCategory();
    }


    /**
     * Вызывается на стр корзины
     */
    private function routeBasket() {
        // корзина
        $this->sendData['pageType'] = 4;
    }


    /**
     * Вызывается на страницАХ заказа
     */
    private function routeOrder() {
        // оформление заказа
        $this->sendData['pageType'] = 5;
    }


    /**
     * Вызывается на страницЕ "Спасибо за заказ"
     */
    private function routeOrderComplete() {
        $this->routeOrder();
        // последняя страница оформление заказа
        $this->sendData['pageType'] = 6;
    }




    /**
     * @param $name
     * @return null
     */
    private function getParam($name) {
        return array_key_exists($name, $this->params) ? $this->params[$name] : null;
    }


    private function d($var = null, $info = '') {
        print '<pre>Debug:';
        if ($info) {
            print '###';
            print_r($info);
            print ' - - - - ';
        }
        if ($var) print_r($var);
        print '</pre>';
    }

}