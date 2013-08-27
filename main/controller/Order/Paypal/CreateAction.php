<?php

namespace Controller\Order\Paypal;

class CreateAction {
    /**
     * @return \Model\Order\CreatedEntity[]
     * @throws \Exception
     */
    public function saveOrders() {
        $request = \App::request();
        $user = \App::user();
        $userEntity = $user->getEntity();
        $cart = $user->getCart();

        $deliveryTypeId = \Model\DeliveryType\Entity::TYPE_SELF;
        $paymentMethodId = \Model\PaymentMethod\Entity::PAYPAL_ID;

        $data = [];
        /** @var $deliveryType \Model\DeliveryType\Entity|null */
        $deliveryType = \RepositoryManager::deliveryType()->getEntityByToken($deliveryTypeId);
        if (!$deliveryType) {
            throw new \Exception(sprintf('Неизвестный метод доставки %s', $deliveryTypeId));
        }

        $orderData = [
            'type_id'                   => \Model\Order\Entity::TYPE_ORDER,
            'geo_id'                    => $user->getRegion()->getId(),
            'user_id'                   => $userEntity ? $userEntity->getId() : null,
            'is_legal'                  => $userEntity ? $userEntity->getIsCorporative() : false,
            'payment_id'                => $paymentMethodId,
            'credit_bank_id'            => null,
            'last_name'                 => null,
            'first_name'                => null,
            'email'                     => null,
            'mobile'                    => null,
            'address_street'            => null,
            'address_number'            => null,
            'address_building'          => null,
            'address_apartment'         => null,
            'address_floor'             => null,
            'extra'                     => 'Заказ через PayPal',
            'svyaznoy_club_card_number' => null,
            'delivery_type_id'          => $deliveryType->getId(),
            'delivery_period'           => null,
            'delivery_date'             => null,
            'ip'                        => $request->getClientIp(),
            'product'                   => [],
            'service'                   => [],
            'payment_params'            => [],
            'shop_id'                   => null,
            'subway_id'                 => null,
        ];

        $cartProduct = $cart->getPaypalProduct();
        if (!$cartProduct) {
            throw new \Exception('В корзине нет товаров, оплачиваемых через PayPal');
        }

        $orderData['product'][] = [
            'id'       => $cartProduct->getId(),
            'quantity' => $cartProduct->getQuantity(),
        ];

        // скидки
        $orderData['action'] = [];

        // мета-теги
        if (\App::config()->order['enableMetaTag']) {
            try {
                /** @var $products \Model\Product\Entity[] */
                $products = [];
                \RepositoryManager::product()->prepareCollectionById([$cartProduct->getId()], $user->getRegion(), function($data) use(&$products) {
                    foreach ($data as $item) {
                        $products[] = new \Model\Product\Entity($item);
                    }
                }, function(\Exception $e) { \App::exception()->remove($e); });
                \App::coreClientV2()->execute();

                foreach ($products as $product) {
                    $partners = [];
                    if ($partnerName = \App::partner()->getName()) {
                        $partners[] = \App::partner()->getName();
                    }
                    if (\Partner\Counter\MyThings::isTracking()) {
                        $partners[] = \Partner\Counter\MyThings::NAME;
                    }
                    foreach (\Controller\Product\BasicRecommendedAction::$recomendedPartners as $recomPartnerName) {
                        if ($viewedAt = \App::user()->getRecommendedProductByParams($product->getId(), $recomPartnerName, 'viewed_at')) {
                            if ((time() - $viewedAt) <= 30 * 24 * 60 * 60) { // 30 дней
                                $partners[] = $recomPartnerName;
                            } else {
                                \App::user()->deleteRecommendedProductByParams($product->getId(), $recomPartnerName, 'viewed_at');
                            }
                        }
                    }
                    $orderData['meta_data'] = \App::partner()->fabricateCompleteMeta(
                        isset($orderData['meta_data']) ? $orderData['meta_data'] : [],
                        \App::partner()->fabricateMetaByPartners($partners, $product)
                    );
                }
                \App::logger()->info(sprintf('Создается заказ от партнеров %s', json_encode($orderData['meta_data']['partner'])), ['order', 'partner']);
            } catch (\Exception $e) {
                \App::logger()->error($e, ['order', 'partner']);
            }
        }

        $data[] = $orderData;

        $params = [];
        if ($userEntity && $userEntity->getToken()) {
            $params['token'] = $userEntity->getToken();
        }

        $result = \App::coreClientV2()->query('order/create-packet', $params, $data);
        \App::logger()->info(['action' => __METHOD__, 'core.response' => $result], ['order']);
        if (!is_array($result)) {
            throw new \Exception('Заказ не подтвержден');
        }

        /** @var $createdOrders \Model\Order\CreatedEntity[] */
        $createdOrders = [];
        foreach ($result as $orderData) {
            if (!is_array($orderData)) {
                \App::logger()->error(sprintf('Получены неверные данные для созданного заказа %s', json_encode($orderData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)), ['order']);
                continue;
            }
            $createdOrder = new \Model\Order\CreatedEntity($orderData);

            // если не получен номер заказа
            if (!$createdOrder->getNumber()) {
                \App::logger()->error(sprintf('Не получен номер заказа %s', json_encode($orderData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)), ['order']);
                continue;
            }
            // если заказ не подтвержден
            if (!$createdOrder->getConfirmed()) {
                \App::logger()->error(sprintf('Заказ не подтвержден %s', json_encode($orderData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)), ['order']);
            }

            $createdOrders[] = $createdOrder;
            \App::logger()->info(sprintf('Заказ успешно создан %s', json_encode($orderData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)), ['order']);
        }

        return $createdOrders;
    }
}