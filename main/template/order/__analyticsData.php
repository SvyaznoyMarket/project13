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

    $recommendationProductIds = [];
    $isUsedCartRecommendation = false;
    if ($sessionName = \App::config()->product['recommendationSessionKey']) {
        $recommendationProductIds = \App::session()->get($sessionName, []);
        \App::session()->set($sessionName, []);
    }

    $data = [];
    foreach ($orders as $order) {
        $orderData = [
            'number'       => $order->getNumber(),
            'numberErp'    => $order->getNumberErp(),
            'address'      => $order->getAddress(),
            'subway'       => $order->getSubwayId() ? [
                'id' => $order->getSubwayId(),
            ] : null,
            'region'       => [
                'id' => $order->getRegionId(),
                'name'  => \App::user()->getRegion() ? \App::user()->getRegion()->getName() : ''
            ],
            'deliveredAt'  => $order->getDeliveredAt() instanceof \DateTime ? $order->getDeliveredAt()->format('Y-m-d') : null,
            'createdAt'    => $order->getCreatedAt() instanceof \DateTime ? $order->getCreatedAt()->format('Y-m-d') : null,
            'delivery'     => array_map(function(\Model\Order\Delivery\Entity $delivery) { return [
                'id'     => $delivery->getId(),
                'typeId' => $delivery->getTypeId(),
                'price'  => $delivery->getPrice(),
            ];  }, $order->getDelivery()),
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
            'coupon_number' => $order->getCouponNumber(),
            'is_partner'    => $order->getIsPartner()
        ];

        foreach ($order->getProduct() as $orderProduct) {
            $product = isset($productsById[$orderProduct->getId()]) ? $productsById[$orderProduct->getId()] : null;
            if (!$product) {
                \App::logger()->error(sprintf('Товар #%s не найден', $orderProduct->getId()), ['order']);
                continue;
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

            // устанавливаем флаг который сигнализирует, что среди списка товаров заказа имеется товар рекомендованный RR
            if (in_array($product->getId(), $recommendationProductIds)) {
                $isUsedCartRecommendation = true;
            }
        }

        $data['orders'][] = $orderData;
    }

    $data['isUsedCartRecommendation'] = $isUsedCartRecommendation;
?>

<div id="jsOrder" data-value="<?= $helper->json($data) ?>"></div>

<? };