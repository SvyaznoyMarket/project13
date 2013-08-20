<?php

return function(
    \Helper\TemplateHelper $helper,
    array $orders,
    array $productsById
) {
    /**
     * @var \Model\Order\Entity[] $orders
     * @var \Model\Product\Entity[] $productsById
     */

    $data = [];
    foreach ($orders as $order) {
        $orderData = [
            'number'       => $order->getNumber(),
            'address'      => $order->getAddress(),
            'region'       => [
                'id' => $order->getRegionId(),
            ],
            'deliveredAt'  => $order->getDeliveredAt() instanceof \DateTime ? $order->getDeliveredAt()->format('Y-m-d') : null,
            'createdAt'    => $order->getCreatedAt(),
            'delivery'     => [
                'id' => $order->getDeliveryTypeId(),
            ],
            'firstName'    => $order->getFirstName(),
            'lastName'     => $order->getLastName(),
            'interval'     => $order->getInterval() ? [
                'start' => $order->getInterval()->getStart(),
                'end'   => $order->getInterval()->getEnd(),
            ] : null,
            'isCorporative' => $order->getIsLegal(),
            'phonenumber'   => $order->getMobilePhone(),
            'paySum'        => $order->getPaySum(),
            'sum'           => $order->getSum(),
            'shop'          => [
                'id' => $order->getShopId(),
            ],
            'paymentMethod' => [
                'id' => $order->getPaymentId(),
            ],
        ];

        $data[] = $orderData;
    }

?>

<div id="jsOrder" data-value="<?= $helper->json($data) ?>"></div>

<? };