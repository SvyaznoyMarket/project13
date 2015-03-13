<?php

namespace View\Order;

use \Model\Order\Product\Entity as OrderProduct;
use \Model\Order\Entity as Order;
use \View\Order\Form as OrderForm;

/**
 * Class CompletePage
 * @package View\Order
 * @deprecated
 */
class CompletePage extends Layout {
    public function prepare() {
        $orders = is_array($this->getParam('orders')) ? $this->getParam('orders') : [];
        /** @var $order \Model\Order\Entity */
        $order = reset($orders);
        if ($order) {
            // если банк Ренессанс
            if (($order->getCredit() instanceof \Model\Order\Credit\Entity) && (\Model\CreditBank\Entity::PROVIDER_DIRECT_CREDIT == $order->getCredit()->getBankProviderId())) {
                $this->addStylesheet('http://api.direct-credit.ru/style.css');
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


    public function slotGoogleAnalytics() {
        $orders = $this->getParam('orders');
        $productsById = $this->getParam('productsById');
        $servicesById = $this->getParam('servicesById');

        $isOrderAnalytics = $this->getParam('isOrderAnalytics');
        $isOrderAnalytics = (null !== $isOrderAnalytics) ? $isOrderAnalytics : true;


        return $isOrderAnalytics ? $this->render('_googleAnalytics', ['orders' => $orders, 'productsById' => $productsById, 'servicesById' => $servicesById, 'isOrderAnalytics' => $isOrderAnalytics]) : $this->render('_googleAnalytics');
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

    public function slotAdblender() {
        // For ports.js analytics
        return \App::config()->analytics['enabled'] ? '<div id="adblenderCommon" class="jsanalytics" data-vars="'.$this->json(['layout' => 'layout-order-complete']).'"></div>' : '';
    }

    public function slotFlocktoryExchangeJS() {
        if (!\App::config()->flocktoryExchange['enabled']) return;

        $orders = $this->getParam('orders');
        if (empty($orders) || !is_array($orders)) {
            return;
        }

        /** @var $order Order */
        $order = reset($orders);
        /** @var $form OrderForm */
        $form = $this->getParam('form');
        if (!$order instanceof Order || !$form instanceof OrderForm) {
            return;
        }

        $email = $form->getEmail();
        if (!$email) {
            $email = $order->getMobilePhone() . '@unknown.email';
        }

        $data = [
            'spot' => 'thankyou',
            'email' => $email,
            'name' => $form->getFirstName() . ' ' . $form->getLastName(),
            'container' => 'flocktory_exchange' // DOM element in which banner will be inserted.
        ];

        return
            '<div id="flocktory_exchange"></div>
            <div id="flocktoryExchangeJS" class="jsanalytics" data-value="' . $this->json($data) . '"></div>';
    }

    public function slotRevolverJS()
    {
        if (!\App::config()->partners['Revolver']['enabled']) return '';

        $content = parent::slotRevolverJS();

        if (is_array($this->getParam('orders')) && (bool)$this->getParam('orders')) {
            $content .= parent::revolverOrdersJS($this->getParam('orders'));
        }

        return $content;
    }

    public function slotGetIntentJS() {
        if (!\App::config()->partners['GetIntent']['enabled']) {
            return '';
        }

        $data = [];

        if (!$this->getParam('sessionIsReaded') && \App::partner()->getName() === 'blackfridaysale') {
            $data['orders'] = [];

            $orders = $this->getParam('orders');
            if (is_array($orders)) {
                foreach ($orders as $order) {
                    /** @var \Model\Order\Entity $order */
                    if ($order instanceof \Model\Order\Entity) {
                        $order2 = ['id' => $order->getNumber(), 'products' => [], 'revenue' => 0];

                        foreach ($order->getProduct() as $orderProduct) {
                            $order2['products'][] = ['id' => (string)$orderProduct->getId(), 'price' => (string)$orderProduct->getPrice(), 'quantity' => (int)$orderProduct->getQuantity()];
                            $order2['revenue'] += $orderProduct->getSum();
                        }

                        $order2['revenue'] = (string)$order2['revenue'];
                        $data['orders'][] = $order2;
                    }
                }
            }
        }

        return '<div id="GetIntentJS" class="jsanalytics" data-value="' . $this->json($data) . '"></div>';
    }
}
