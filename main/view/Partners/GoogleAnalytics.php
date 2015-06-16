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
                case "orderV3.complete":
                    $this->routeOrderComplete();
                    break;

                default:
                    $this->routeDefault();
            }
            // end of case

        } catch (\Exception $e) {
            \App::logger()->error($e, [__CLASS__]);
            \App::exception()->remove($e);
        }

        if (\App::user()->getRegion()) $this->sendData['vars']['dimension14'] = \App::user()->getRegion()->getName();

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
        $enterCookie = \App::request()->cookies->get('enter');
        if (empty($enterCookie)) {
            $userEntity = $this->user->getEntity();
            $this->sendData['vars']['dimension7'] = ($userEntity && $userEntity->getId()) ?
                'Registered' :
                'Anonymous';
        }
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

        if (!empty($_SERVER['HTTP_REFERER'])) {
            if (strpos($_SERVER['HTTP_REFERER'], 'search?q=') > 0) {
                $this->sendData['afterSearch'] = 1;
            }
        }

        if ($product instanceof \Model\Product\Entity) {
            $this->sendData['vars']['dimension11'] = $product->getArticle();

            $this->sendData['vars']['dimension10'] =
                ($product->isInShopOnly() || $product->isInShopStockOnly())
                    ? 'NO'
                    : 'YES';
        }

        if ($categoryUpper) {
            $this->sendData['vars']['dimension6'] = $categoryUpper->getName();  // Имя верхней категории
            $this->sendData['upperCat'] = $categoryUpper->getName();            // Имя верхней категории
        }
        if ($categoryDown) {
            $this->sendData['vars']['dimension12'] = $categoryDown->getName();  // Имя текущей подкатегории
        }
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
            /** @var \Model\Product\Category\Entity $categoryUpper */
            $categoryUpper = reset($categories);
            /** @var \Model\Product\Category\Entity $categoryUpper */
            $categoryDown = end($categories);

            if ($categoryUpper) {
                $this->sendData['vars']['dimension6'] = $categoryUpper->getName();  // Имя верхней категории
            }
            if ($categoryDown) {
                $this->sendData['vars']['dimension12'] = $categoryDown->getName();  // Имя текущей подкатегории
            }
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
        $userEntity = $this->user->getEntity();
        $cartProductsById = $this->getParam('cartProductsById');
        /** @var $products \Model\Product\Entity[] */
        $products = $this->getParam('products');

        if (!$cartProductsById) return false;

        foreach ($products as $product) {
            if (!isset($cartProductsById[$product->getId()])) continue;
            $SKUs .= $product->getArticle() . ',';
            $total += $product->getPrice();
        }

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
     * Метод не нужен, напрямую трекаем код на стр.
     * main/template/error/page-404.php
     */
    /*
    private function route404() {
        $this->sendData['vars']['dimension5'] = '404';
    }*/


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
        /** @var  $paymentMethod \Model\PaymentMethod\Entity  */
        //$paymentMethod = $this->getParam('paymentMethod');
        /** @var $ordersAll \Model\Order\Entity[] */
        $ordersAll = $this->getParam('orders');
        if (!$ordersAll) return false;
        /** @var $orders \Model\Order\Entity **/

        $this->sendData['ecommerce'] = [];

        foreach($ordersAll as $order) {
            /** @var $order \Model\Order\Entity **/
            if (!$order->getNumber()) {
                continue;
            }

            $addTransaction = [
                'id'        => $order->getNumber(), // Transaction ID. Required.
                'revenue'   => $order->getSum(), // Grand Total.
                'shipping'  => 0, //$delivery instanceof Delivery ? $delivery->getPrice() : 0, // Shipping.
            ];

            $products = $order->getProduct();
            $purchasedProducts = []; // купленные товары
            foreach($products as $orderProduct) {
                /** @var $orderProduct  \Model\Order\Product\Entity **/
                /** @var $product       \Model\Product\Entity       **/

                $product = isset($productsById[$orderProduct->getId()]) ? $productsById[$orderProduct->getId()] : false;

                $categoryName = null;
                $mainCategory = $product ? $product->getRootCategory() : null;
                $parentCategory = $product ? $product->getParentCategory() : null;

                $productName = $product ? $product->getName() : '';
                $RR_buy_viewed = false;
                $RR_buy_added = false;
                $RR_buy_block = null;
                if (isset($order->meta_data[sprintf('product.%s.sender', $product->getUi())][0])) {
                    if ($order->meta_data[sprintf('product.%s.sender', $product->getUi())][0] == 'retailrocket') {
                        $productName .= sprintf(' (RR_%s)', @$order->meta_data[sprintf('product.%s.position', $product->getUi())][0]);
                        $RR_buy_block = @$order->meta_data[sprintf('product.%s.position', $product->getUi())][0];
                        if (isset($order->meta_data[sprintf('product.%s.from', $product->getUi())][0])) $RR_buy_viewed = true;
                        else $RR_buy_added = true;
                    }
                }

                if ($mainCategory || $parentCategory) {
                    $categoryName .= implode(array_filter([$mainCategory->getName(), $parentCategory->getName()]), ' ');
                }

                $purchasedProducts[] = [
                    'id'        => $order->getNumber(),
                    'name'      => $productName,
                    'sku'       => $product ? $product->getArticle() : null,
                    'category'  => $categoryName,
                    'price'     => $orderProduct->getPrice(),
                    'quantity'  => $orderProduct->getQuantity(),
                    'rr_added'  => $RR_buy_added,
                    'rr_viewed' => $RR_buy_viewed,
                    'rr_block'  => $RR_buy_block
                ];
            }

            // $paymentMethod->getName(),// '<Тип оплаты>'

            // совершенный заказ
            $completedOrders = [
                'dimension2' => $order->getDeliveryTypeId(),// '<Тип доставки>',
                'dimension3' => $order->getCouponNumber(),// '<Код купона>',
                'dimension4' => $order->getPaymentId(),// '<Тип оплаты>'
            ];

            $this->sendData['ecommerce'][] = [
                'addTransaction' => $addTransaction,
                'items'          => $purchasedProducts,
                'send'           => $completedOrders,
            ];
        }

        /**
         * Нужно не забывать, что пока купон может быть только у одного заказа (первого и последнего)
         * если разбивается на несколько, то все скидки и купоны удаляются
         */
    }


    /**
     * Удаляет последний символ в строке
     *
     * @param $str
     * @return string
     */
    private function rmLastSeporator(&$str) {
        $str = (string) $str;
        if (isset($str[1])) {
            $str = substr($str, 0, strlen($str)-1);
        }
        return $str;
    }


    /**
     * Возвращает параметр лэйаута.
     * Метод нужен для связи между параметрами из Layout
     * Т.о. настоящий класс имеет доступ ко всем параметрам, что и Layout из которого этот класс вызван
     *
     * @param $name
     * @return null
     */
    private function getParam($name) {
        return array_key_exists($name, $this->params) ? $this->params[$name] : null;
    }

}
