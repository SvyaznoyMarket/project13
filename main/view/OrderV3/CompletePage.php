<?php

namespace View\OrderV3;

class CompletePage extends Layout {

    public function prepare() {
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
    }

    public function slotContent() {
        $path = 'order-v3';

        $region = \App::user()->getRegion();
        if ($region && \App::config()->newOrder) {
            $ordersNewTest = \App::abTest()->getTest('orders_new');
            $ordersNewSomeRegionsTest = \App::abTest()->getTest('orders_new_some_regions');
            if (
                (!in_array($region->getId(), [93746, 119623]) && $ordersNewTest && in_array($ordersNewTest->getChosenCase()->getKey(), ['new_2'], true)) // АБ-тест для остальных регионов
                || (in_array($region->getId(), [93746, 119623]) && $ordersNewSomeRegionsTest && in_array($ordersNewSomeRegionsTest->getChosenCase()->getKey(), ['new_2'], true)) // АБ-тест для Ярославля и Ростова-на-дону
            ) {
                $path = 'order-v3-new';
            }
        }

        return \App::closureTemplating()->render( $path . '/page-complete', $this->params);
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

        return $html;
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
