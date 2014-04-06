<?php

namespace View\Order;

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
        return $this->tryRender('partner-counter/_cpaexchange_conversion');
    }

    public function slotСpaexchangeJS () {
        return '<div id="cpaexchangeJS" class="jsanalytics" data-value="' . $this->json(['id' => 25015]) . '"></div>';
    }
}
