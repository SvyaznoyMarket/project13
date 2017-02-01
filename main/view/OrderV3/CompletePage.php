<?php

namespace View\OrderV3;

use Session\AbTest\ABHelperTrait;

class CompletePage extends Layout {
    use ABHelperTrait;

    /** @var \Model\Order\Entity[] */
    private $orders;

    public function slotOrderHead() {
        return \App::closureTemplating()->render('order-v3-new/__head', ['step' => 3]);
    }

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
                if (!$order->isCredit()) continue;

                // Данные для "Купи-в-кредит"
                $data = new Credit\Kupivkredit($order, $this->params['productsById']);
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
                    'phone'  => preg_replace('/^8/', '', $order->mobilePhone),
                ];

                foreach ($order->getProduct() as $orderProduct) {
                    /** @var $product \Model\Product\Entity|null */
                    $product = isset($this->params['productsById'][$orderProduct->getId()]) ? $this->params['productsById'][$orderProduct->getId()] : null;
                    if (!$product) {
                        throw new \Exception(sprintf('Не найден товар #%s, который есть в заказе', $orderProduct->getId()));
                    }

                    $creditData[$order->getNumber()]['direct-credit']['vars']['items'][] = [
                        'name'     => $product->getName(),
                        'quantity' => (int)$orderProduct->getQuantity(),
                        'price'    => (int)$orderProduct->getPrice(),
                        'articul'  => $product->getArticle(),
                        'type'     => \RepositoryManager::creditBank()->getCreditTypeByCategoryToken($product->getRootCategory() ? $product->getRootCategory()->getToken() : null)
                    ];
                }
            }
        }
        $this->setParam('creditData', $creditData);
    }

    public function slotContent() {
        $template = 'page-complete';
        $orders = $this->getParam('orders');
        if (('call-center' !== \App::session()->get(\App::config()->order['channelSessionKey'])) && (1 === count($orders))) {
            $template = 'page-complete-single';
        }
        return \App::closureTemplating()->render('order-v3-new/' . $template, $this->params);
    }

    public function slotBodyDataAttribute() {
        return 'order-v3-new';
    }

    public function slotPartnerCounter()
    {
        $html = parent::slotPartnerCounter();

        // Sociomantic - передаём все заказы!
        $html .= '<div id="sociomanticOrderCompleteJS" class="jsanalytics" ></div>';

        if (\App::config()->partners['MyThings']['enabled'] && \App::partner()->getName() == 'mythings') {
            /** @var $order \Model\Order\Entity */
            $order = reset($this->orders);
            $data = [
                'EventType' => 'Conversion',
                'Action'    => '9902',
                'Products'  => array_map(function(\Model\Order\Product\Entity $p) {
                    return ['id' => (string)$p->getId(), 'price' => (string)$p->getPrice(), 'qty' => $p->getQuantity()];
                }, $order->getProduct()),
                'TransactionReference'  => $order->getNumber(),
                'TransactionAmount'     => (string)$order->getSum()
            ];
            $html .= sprintf('<div id="MyThingsJS" class="jsanalytics" data-value="%s"></div>', $this->json($data));
        }

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

        foreach ($orders as $order) {
            /* Основные данные для GTM */
            $orderData = [
                'orderNumber' => $order->getNumber(),
                'orderSum' => $order->getSum(),
            ];

            if (\App::partner()->getName()) $orderData['partner'] = \App::partner()->getName();

            /* Дополнительные данные для LinkProfit */
            if (\App::config()->partners['LinkProfit']['enabled'] && \App::partner()->getName() == 'linkprofit') {
                $orderData['webmaster_id'] = \App::request()->cookies->get(\App::config()->partners['LinkProfit']['cookieName'], 0);
            }

            $data[] = $orderData;
        }

        return parent::slotGoogleTagManagerJS($data);
    }

}
