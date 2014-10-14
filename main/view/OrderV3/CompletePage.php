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
        return \App::closureTemplating()->render('order-v3/page-complete', $this->params);
    }

    public function slotBodyDataAttribute() {
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


}
