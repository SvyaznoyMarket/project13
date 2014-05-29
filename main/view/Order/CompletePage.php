<?php

namespace View\Order;

use \Model\Order\Product\Entity as OrderProduct;
use \Model\Order\Entity as Order;

class CompletePage extends Layout {
    public function prepare() {
        $orders = is_array($this->getParam('orders')) ? $this->getParam('orders') : [];
        /** @var $order \Model\Order\Entity */
        $order = reset($orders);
        if ($order) {
            // если банк Ренессанс
            if (($order->getCredit() instanceof \Model\Order\Credit\Entity) && (\Model\CreditBank\Entity::PROVIDER_DIRECT_CREDIT == $order->getCredit()->getBankProviderId())) {
                $this->addStylesheet('http://direct-credit.ru/widget/style.css');
            }
        }

        $this->setTitle('Оформление заказа - Enter');
    }

    public function slotContent() {
        return $this->render('order/page-complete', $this->params);
    }

    public function slotBodyDataAttribute() {
        return 'order_complete';
    }

    // public function slotYandexMetrika() {
    //     return (\App::config()->yandexMetrika['enabled']) ? $this->render('order/_yandexMetrika') : '';
    // }

    public function slotGoogleAnalytics() {
        $orders = $this->getParam('orders');
        $productsById = $this->getParam('productsById');
        $servicesById = $this->getParam('servicesById');

        $isOrderAnalytics = $this->getParam('isOrderAnalytics');
        $isOrderAnalytics = (null !== $isOrderAnalytics) ? $isOrderAnalytics : true;


        return $isOrderAnalytics ? $this->render('_googleAnalytics', ['orders' => $orders, 'productsById' => $productsById, 'servicesById' => $servicesById, 'isOrderAnalytics' => $isOrderAnalytics]) : $this->render('_googleAnalytics');
    }

    public function slotInnerJavascript() {
        /** @var $orders \Model\Order\Entity[] */
        $orders = $this->getParam('orders');
        $productsById = $this->getParam('productsById');

        $isOrderAnalytics = $this->getParam('isOrderAnalytics');

        $isOrderAnalytics = (null !== $isOrderAnalytics) ? $isOrderAnalytics : true;

        $tag_params = ['prodid' => [], 'pname' => [], 'pcat' => [], 'purchasevalue' => 0, 'pagetype' => 'purchase'];
        foreach ($orders as $order) {
            foreach ($order->getProduct() as $orderProduct) {
                if (!isset($productsById[$orderProduct->getId()])) continue;
                /** @var $product \Model\Product\Entity */
                $product = $productsById[$orderProduct->getId()];
                $categories = $product->getCategory();
                $category = array_pop($categories);

                $tag_params['prodid'][] = $product->getId();
                $tag_params['pname'][] = $product->getName();
                $tag_params['pcat'][] = $category ? $category->getToken() : '';
                $tag_params['purchasevalue'] += $order->getPaySum();
            }
        }

        return ''
        . ($isOrderAnalytics ? $this->render('_remarketingGoogle', ['tag_params' => $tag_params]) : '')
        . "\n\n"
        . $this->render('_innerJavascript');
    }

    public function slotAdriver() {
        //Adriver данные выводятся через order/_analitycs
        return '';
    }

    public function slotMarinConversionTagJS()
    {
        $sessionIsReaded = $this->getParam('sessionIsReaded');
        if ($sessionIsReaded) return '';

        $paymentPageType = $this->getParam('paymentPageType');
        if ( !isset($paymentPageType) || !$paymentPageType === 'complete' ) {
            return '';
        }

        $orders = $this->getParam('orders');
        $dataOrders = [];
        if ( !empty($orders) ) {
            $dataOrders['currency'] = 'RUB';
            $dataOrders['items'] = [];
            foreach ($orders as $order) {
                /* @var $order \Model\Order\Entity */
                $dataOrders['items'][] = [
                    'orderId'   => $order->getNumber(),
                    'price'     => $order->getPaySum(),
                    'convType'  => 'sales',
                ];
            }
        }

        return '<div id="marinConversionTagJS" class="jsanalytics" data-value="' . $this->json($dataOrders) . '" >
                <noscript><img src="https://tracker.marinsm.com/tp?act=2&cid=7saq97byg0&script=no" ></noscript></div>';

    }

    public function slotСpaexchangeConversionJS () {
        if ( !\App::config()->partners['Сpaexchange']['enabled'] ) {
            return;
        }

        return $this->tryRender('partner-counter/_cpaexchange_conversion');
    }

    public function slotСpaexchangeJS () {
        if ( !\App::config()->partners['Сpaexchange']['enabled'] ) {
            return;
        }

        return '<div id="cpaexchangeJS" class="jsanalytics" data-value="' . $this->json(['id' => 25015]) . '"></div>';
    }

    public function slotRevolvermarketingConversionJS () {
        if ( !\App::config()->partners['Revolvermarketing']['enabled'] ) {
            return;
        }

        return $this->tryRender('partner-counter/_revolvermarketing_conversion');
    }

    public function slotAdLensJS () {
        if ( !\App::config()->partners['AdLens']['enabled'] ) {
            return;
        }

        $orders = $this->getParam('orders');
        if (empty($orders)) {
            return;
        }

        $dataOrders = [
            'orders'  => null,//обозначение факта заказа
            'revenue' => null,//текущая сумма заказа в корзине
            'margin'  => 0,   //прибыль заказа
            'items'   => null,//количество товаров в заказе
            'transid' => null,//уникальный идентификатор транзакции
        ];

        $transid = null;
        $revenue = 0;
        $items = 0;
        foreach ($orders as $order) {
            if (!$order instanceof \Model\Order\Entity) {continue;}

            $revenue += $order->getSum();

            foreach ($order->getProduct() as $product) {
                $items += $product->getQuantity();
            }

            if (!$transid) {
                $transid = $order->getId();
            }
        }

        if (!$revenue || !$items || !$transid) {
            return;
        }

        $dataOrders = array_merge($dataOrders, [
            'orders' => 1,
            'revenue' => $revenue,
            'items' => $items,
            'transid' => $transid,
        ]);

        return
            '<div id="AdLensJS" class="jsanalytics" data-value="' . $this->json($dataOrders) . '">
                <noscript><img src="http://pixel.everesttech.net/245/t?ev_Orders='.$dataOrders['orders'].'&ev_Revenue='.$dataOrders['revenue'].'&ev_Margin='.$dataOrders['margin'].'&ev_Items='.$dataOrders['items'].'&ev_transid='.$dataOrders['transid'].'" width="1" height="1"/></noscript>
            </div>';
    }

    public function slotAdblender() {
        // For ports.js analytics
        return \App::config()->analytics['enabled'] ? '<div id="adblenderCommon" class="jsanalytics" data-vars="'.$this->json(['layout' => 'layout-order-complete']).'"></div>' : '';
    }

    public function slotRuTargetOrderCompleteJS() {
        if (!\App::config()->partners['RuTarget']['enabled']) return;

        /** @var $orders Order[] */
        $orders = $this->getParam('orders');
        if (!$orders || empty($orders) || !is_array($orders)) return;

        $productList = [];
        foreach ($orders as $order) {
            if (!$order instanceof Order) continue;

            foreach ($order->getProduct() as $product) {
                if (!$product instanceof OrderProduct) continue;

                $productList[] = [
                    'qty' => $product->getQuantity(),
                    'sku' => $product->getId(),
                ];
            }
        }

        $data = [
            'products' => $productList,
            'regionId' => \App::user()->getRegionId(),
        ];

        return "<div id=\"RuTargetOrderCompleteJS\" class=\"jsanalytics\" data-value=\"" . $this->json($data) . "\"></div>";

    }

    public function slotLamodaCompleteJS() {
        if (!\App::config()->partners['Lamoda']['enabled']) return;

        return '<div id="LamodaCompleteJS" class="jsanalytics"></div>';
    }

    public function slotMyragonOrderCompleteJS() {
        $config = \App::config()->partners['Myragon'];
        if (!$config['enabled'] || !$config['enterNumber'] || !$config['secretWord'] || !$config['subdomainNumber']) {
            return;
        }

        /** @var $orders Order[] */
        $orders = $this->getParam('orders');
        if (!$orders || empty($orders) || !is_array($orders)) return;

        $orderList = [];
        foreach ($orders as $order) {
            if (!$order instanceof Order || !$order->getId()) continue;

            $orderList[] = [
                'order_id' => $order->getId(),
                'hash' => md5($config['enterNumber'] . $config['secretWord'] . $order->getId()),
            ];
        }

        $data = [
            'config' => [
                'enterNumber' => $config['enterNumber'], // номер Вашей кампании
                'secretWord' => $config['secretWord'], // секретное слово
                'subdomainNumber' => $config['subdomainNumber'], // номер поддомена в сервисе Myragon
            ],
            'orderList' => $orderList,
        ];

        return '<div id="myragonOrderCompleteJS" class="jsanalytics" data-value="' . $this->json($data) . '"></div>';
    }
}
