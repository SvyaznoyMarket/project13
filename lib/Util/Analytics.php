<?php

namespace Util;

class Analytics {
    /**
     * @param \Model\Order\Entity[] $orders
     * @param \Model\Product\Entity[]|null $productsById
     * @return array
     */
    public static function getForOrder($orders, $productsById = null) {
        if ($productsById === null) {
            $productsById = [];

            foreach ($orders as $order) {
                foreach ($order->getProduct() as $orderProduct) {
                    $productsById[$orderProduct->getId()] = new \Model\Product\Entity(['id' => $orderProduct->getId()]);
                }
            }

            if ($productsById) {
                \RepositoryManager::product()->prepareProductQueries($productsById, 'media category brand');
                \App::coreClientV2()->execute();
                \RepositoryManager::review()->addScores($productsById);
            }
        }

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
                'delivery'     => [
                    'id'     => $order->getDelivery()->getId(),
                    'typeId' => $order->getDelivery()->getTypeId(),
                    'price'  => $order->getDelivery()->getPrice(),
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
                'coupon_number' => $order->getCouponNumber(),
                'is_partner'    => $order->getIsPartner(),
                'isSlot'        => $order->isSlot(),
                'isCredit'      => $order->isCredit()
            ];

            foreach ($order->getProduct() as $orderProduct) {
                $product = isset($productsById[$orderProduct->getId()]) ? $productsById[$orderProduct->getId()] : null;
                if (!$product) {
                    \App::logger()->error(sprintf('Товар #%s не найден', $orderProduct->getId()), ['order']);
                    continue;
                }

                $compareProduct = static::getCompareProduct($product->getUi());

                $productData = [
                    'id'       => $product->getId(),
                    'ui'       => $product->getUi(),
                    'quantity' => $orderProduct->getQuantity(),
                    'price'    => $orderProduct->getPrice(),
                    'article'  => $product->getArticle(),
                    'barcode'  => $product->getBarcode(),
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
                    'isSlot' => (bool)$product->getSlotPartnerOffer(),
                    'isOnlyFromPartner' => $product->isOnlyFromPartner(),
                    'inCompare' => (bool)$compareProduct,
                    'compareLocation' => isset($compareProduct['location']) ? $compareProduct['location'] : '',
                ];

                if (isset($order->meta_data[sprintf('product.%s.sender', $product->getUi())])) $productData['sender'] = $order->meta_data[sprintf('product.%s.sender', $product->getUi())][0];
                if (isset($order->meta_data[sprintf('product.%s.position', $product->getUi())])) $productData['position'] = $order->meta_data[sprintf('product.%s.position', $product->getUi())][0];
                if (isset($order->meta_data[sprintf('product.%s.method', $product->getUi())])) $productData['method'] = $order->meta_data[sprintf('product.%s.method', $product->getUi())][0];
                if (isset($order->meta_data[sprintf('product.%s.from', $product->getUi())])) $productData['from'] = $order->meta_data[sprintf('product.%s.from', $product->getUi())][0];
                if (isset($order->meta_data[sprintf('product.%s.isFromProductCard', $product->getUi())])) $productData['isFromProductCard'] = $order->meta_data[sprintf('product.%s.isFromProductCard', $product->getUi())][0]; // SITE-5772
                if (isset($order->meta_data[sprintf('product.%s.sender2', $product->getUi())])) $productData['sender2'] = $order->meta_data[sprintf('product.%s.sender2', $product->getUi())][0];

                $orderData['products'][] = $productData;

                // устанавливаем флаг который сигнализирует, что среди списка товаров заказа имеется товар рекомендованный RR
                if (in_array($product->getId(), $recommendationProductIds)) {
                    $isUsedCartRecommendation = true;
                }
            }

            $data['orders'][] = $orderData;
        }

        $data['isUsedCartRecommendation'] = $isUsedCartRecommendation;

        return $data;
    }

    private static function getCompareProduct($productUi) {
        $compareProducts = \App::session()->get(\App::config()->session['compareKey']);
        if (is_array($compareProducts)) {
            foreach ($compareProducts as $compareProduct) {
                if ($compareProduct['ui'] === $productUi) {
                    return $compareProduct;
                }
            }
        }

        return null;
    }
}