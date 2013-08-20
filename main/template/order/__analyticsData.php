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
            'subway'       => $order->getSubwayId() ? [
                'id' => $order->getSubwayId(),
            ] : null,
            'region'       => [
                'id' => $order->getRegionId(),
            ],
            'deliveredAt'  => $order->getDeliveredAt() instanceof \DateTime ? $order->getDeliveredAt()->format('Y-m-d') : null,
            'createdAt'    => $order->getCreatedAt() instanceof \DateTime ? $order->getCreatedAt()->format('Y-m-d') : null,
            'delivery'     => [
                'id' => $order->getDeliveryTypeId(),
            ],
            //'firstName'    => $order->getFirstName(),
            //'lastName'     => $order->getLastName(),
            'interval'     => $order->getInterval() ? [
                'start' => $order->getInterval()->getStart(),
                'end'   => $order->getInterval()->getEnd(),
            ] : null,
            'isCorporative' => $order->getIsLegal(),
            'phonenumber'   => $order->getMobilePhone(),
            'paySum'        => $order->getPaySum(),
            'sum'           => $order->getSum(),
            'shop'          => $order->getShopId() ? [
                'id' => $order->getShopId(),
            ] : null,
            'paymentMethod' => [
                'id' => $order->getPaymentId(),
            ],
            'products'      => [],
        ];

        foreach ($order->getProduct() as $orderProduct) {
            $product = isset($productsById[$orderProduct->getId()]) ? $productsById[$orderProduct->getId()] : null;
            if (!$product) {
                \App::logger()->error(sprintf('Товар #%s не найден', $orderProduct->getId()), ['order']);
            }

            $orderData['products'][] = [
                'quantity' => $orderProduct->getQuantity(),
                'price'    => $orderProduct->getPrice(),
                'article'  => $product->getArticle(),
                'barcode'  => $product->getBarcode(),
                'id'       => $product->getId(),
                'name'     => $product->getName(),
                'token'    => $product->getToken(),
                'link'     => $product->getLink(),
                'brand'    => $product->getBrand() ? [
                    'id'   => $product->getBrand()->getId(),
                    'name' => $product->getBrand()->getName(),
                ] : null,
                'category' => array_map(function(\Model\Product\Category\Entity $category) {
                    return [
                        'id'    => $category->getId(),
                        'name'  => $category->getName(),
                        'token' => $category->getToken(),
                        'link'  => $category->getLink(),
                    ];
                }, $product->getCategory()),
            ];
        }

        $data[] = $orderData;
    }

?>

<div id="jsOrder" data-value="<?= $helper->json($data) ?>"></div>

<? };