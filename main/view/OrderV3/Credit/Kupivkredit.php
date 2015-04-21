<?php

namespace View\OrderV3\Credit;

class Kupivkredit {
    private $config;
    public $details = [];
    public $items = [];
    public $partnerId;
    public $partnerName;
    public $partnerOrderId;
    public $deliveryType;

    /**
     * @param \Model\Order\Entity     $order
     * @param \Model\Product\Entity[] $productsById
     * @throws \Exception
     */
    public function __construct(\Model\Order\Entity $order, array $productsById) {
        $this->config = \App::config()->creditProvider['kupivkredit'];

        foreach ($order->getProduct() as $orderProduct) {
            /** @var $product \Model\Product\Entity|null */
            $product = isset($productsById[$orderProduct->getId()]) ? $productsById[$orderProduct->getId()] : null;
            if (!$product) {
                throw new \Exception(sprintf('Не найден товар #%s, который есть в заказе', $orderProduct->getId()));
            }

            $this->items[] = [
                'title'    => sprintf('%s шт %s', $orderProduct->getQuantity(), $product->getName()), // SITE-2662
                'category' => '',
                'qty'      => 1, //$orderProduct->getQuantity(), // SITE-2662
                'price'    => (int)$orderProduct->getSum(), // SITE-2662
            ];
        }

        $this->details = [
            'firstname'  => $order->getFirstName(),
            'lastname'   => $order->getLastName(),
            'middlename' => $order->getMiddleName(),
            'email'      => '',
            'cellphone'  => $order->getMobilePhone(),
        ];

        $this->partnerId = $this->config['partnerId'];
        $this->partnerName = $this->config['partnerName'];
        $this->partnerOrderId = $order->getNumber();
        $this->deliveryType = '';
    }

    /**
     * @return string
     */
    public function __toString() {
        return base64_encode(json_encode($this));
    }

    /**
     * @return string
     */
    public function getSig() {
        $iterationCount = 1102;

        $sig = (string)$this . $this->config['signature'];
        $sig = md5($sig).sha1($sig);
        for($i = 0; $i < $iterationCount; $i++) {
            $sig = md5($sig);
        }

        return $sig;
    }
}