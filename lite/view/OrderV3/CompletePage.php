<?php

namespace view\OrderV3;


class CompletePage extends \View\OrderV3\Layout
{

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
                $data = new Credit\Kupivkredit($order, $this->params['products']);
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
                        'type'     => \RepositoryManager::creditBank()->getCreditTypeByCategoryToken($product->getRootCategory() ? $product->getRootCategory()->getToken() : null)
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
                    if (is_null($method->getAction($this->getParam('motivationAction'))) && !$method->isSvyaznoyClub()) unset($methods[$paymentKey]->methods[$methodKey]);
                }
            }
            $this->setParam('ordersPayment', $methods);
        }

    }

    public function blockOrderHead() {
        return $this->render('order/common/order-head', ['step' => 3]);
    }

    public function blockContent() {
        return \App::closureTemplating()->render('order/page-complete', $this->params + ['page' => $this]);
    }

}