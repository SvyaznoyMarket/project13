<?php

namespace View\OrderV3;

class CompletePage extends Layout {

    /** @var \Model\Order\Entity[] */
    private $orders;

    public function slotGoogleRemarketingJS($tagParams = []) {

        $ordersSum = 0;

        foreach ($this->orders as $order) {
            $ordersSum += $order->getSum();
        }

        $tagParams = [
            'pagetype'          => 'purchase',
            'ecomm_ordervalue'  => $ordersSum
        ];

        return parent::slotGoogleRemarketingJS($tagParams);
    }

    public function prepare() {

        // Внутренние переменные
        $this->orders = $this->getParam('orders', []);

        $this->setTitle('Оформление заказа - Enter');

        // подготовим данные для кредита, если таковой есть
        $creditData = [];
        if (!empty($this->params['banks'])) {

            $this->addStylesheet('//api.direct-credit.ru/style.css');

            foreach ($this->params['orders'] as $order) {
                /** @var $order \Model\Order\Entity */
                if ($order->paymentId != \Model\PaymentMethod\PaymentMethod\PaymentMethodEntity::PAYMENT_CREDIT) continue;

                // Данные для "Купи-в-кредит"
                $data = new \View\Order\Credit\Kupivkredit($order, $this->params['products']);
                $creditData[$order->getNumber()]['kupivkredit'] = [
                    'widget' => 'kupivkredit',
                    'vars'   => [
                        'sum'   => $order->getProductSum(), // брокеру отпрвляем стоимость только продуктов!
                        'order' => (string)$data,
                        'sig'   => $data->getSig(),
                    ],
                ];

                // Данные для Direct Credit
                $creditData[$order->getNumber()]['direct-credit']['widget'] = 'direct-credit';

                $creditData[$order->getNumber()]['direct-credit']['vars'] = [
                    'partnerID' => \App::config()->creditProvider['directcredit']['partnerId'],
                    'number' => $order->getNumber(),
                    'region' => $order->getShopId() ? $order->getShopId() : ( 'r_' . \App::user()->getRegion()->getParentId() ?: \App::user()->getRegion()->getId() ),
                    'items'  => [],
                ];

                foreach ($order->getProduct() as $orderProduct) {
                    /** @var $product \Model\Product\Entity|null */
                    $product = isset($this->params['products'][$orderProduct->getId()]) ? $this->params['products'][$orderProduct->getId()] : null;
                    if (!$product) {
                        throw new \Exception(sprintf('Не найден товар #%s, который есть в заказе', $orderProduct->getId()));
                    }

                    $creditData[$order->getNumber()]['direct-credit']['vars']['items'][] = [
                        'name'     => $product->getName(),
                        'quantity' => (int)$orderProduct->getQuantity(),
                        'price'    => (int)$orderProduct->getPrice(),
                        'articul'  => $product->getArticle(),
                        'type'     => \RepositoryManager::creditBank()->getCreditTypeByCategoryToken($product->getMainCategory() ? $product->getMainCategory()->getToken() : null)
                    ];
                }
            }
        }
        $this->setParam('creditData', $creditData);

        // в случае онлайн-мотивации отфильтруем методы оплаты
        if (is_string($this->getParam('motivationAction'))) {
            /** @var $methods \Model\PaymentMethod\PaymentEntity[] */
            $methods = $this->getParam('ordersPayment');
            foreach ($methods as $paymentKey => $payment) {
                foreach ($payment->methods as $methodKey => $method) {
                    // убираем все, в которых нет мотивирующей акции
                    if (is_null($method->getAction($this->getParam('motivationAction')))) unset($methods[$paymentKey]->methods[$methodKey]);
                }
            }
            $this->setParam('ordersPayment', $methods);
        }

    }

    public function slotContent() {
        $template = 'page-complete';
        $orders = $this->getParam('orders');
        if (\App::abTest()->isOnlineMotivation(count($orders))) {
            /* @var $order \Model\Order\Entity */
            $order = reset($orders);
            /* Если выбран самовывоз из определенной точки или выбрана доставка с адресом */
            /* Пикпоинт (6) пока исключим, т.к. для него не отдаётся адрес: CORE-2558 */
            if (in_array($order->getDeliveryTypeId(), [3,4]) || $order->getDeliveryTypeId() == 1 || $order->point) $template = 'page-complete_online-motivation';
        }
        return \App::closureTemplating()->render('order-v3-new/' . $template, $this->params);
    }

    public function slotBodyDataAttribute() {
        $region = \App::user()->getRegion();
        if ($region && \App::config()->newOrder) {
            $ordersNewTest = \App::abTest()->getTest('orders_new');
            $ordersNewSomeRegionsTest = \App::abTest()->getTest('orders_new_some_regions');
            if (
                (!in_array($region->getId(), [93746, 119623]) && $ordersNewTest && in_array($ordersNewTest->getChosenCase()->getKey(), ['new_2'], true)) // АБ-тест для остальных регионов
                || (in_array($region->getId(), [93746, 119623]) && $ordersNewSomeRegionsTest && in_array($ordersNewSomeRegionsTest->getChosenCase()->getKey(), ['new_2'], true)) // АБ-тест для Ярославля и Ростова-на-дону
            ) {
                return 'order-v3-new';
            }
        }

        return 'order-v3';
    }

    public function slotPartnerCounter()
    {
        $html = parent::slotPartnerCounter();

        // ActionPay
        $html .= $this->tryRender('partner-counter/_actionpay', ['routeName' => 'order.complete'] );

        // Sociomantic - передаём все заказы!
        $html .= '<div id="sociomanticOrderCompleteJS" class="jsanalytics" ></div>';

        return $html;
    }

    public function slotRevolverJS() {
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

    public function slotGoogleTagManagerJS($data = []) {
        /** @var \Model\Order\Entity[] $orders */
        $orders = $this->getParam('orders', []);
        $data = [];

        /* Дополнительные данные для LinkProfit */
        if (\App::config()->partners['LinkProfit']['enabled'] && \App::partner()->getName() == 'linkprofit') {
            foreach ($orders as $order) {
                $data[] = [
                    'orderNumber' => $order->getNumber(),
                    'orderSum' => $order->getSum(),
                    'webmaster_id' => \App::request()->cookies->get(\App::config()->partners['LinkProfit']['cookieName'], 0)
                ];
            }
        }

        return parent::slotGoogleTagManagerJS($data);
    }

}
