<?php

namespace Controller\Order;

class OneClickAction {
    /**
     * @param \Http\Request $request
     * @return \Http\JsonResponse
     * @throws \Exception\NotFoundException
     * @throws \Exception
     */
    public function execute(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        if (!$request->isXmlHttpRequest()) {
            throw new \Exception\NotFoundException('Request is not xml http request');
        }

        $userEntity = \App::user()->getEntity();

        try {
            $productToken = $request->get('product');
            if (!$productToken) {
                $e = new \Exception\NotFoundException(sprintf('В GET запросе %s не содержится токена товара для заказа в один клик', json_encode($request->query->all())));
                \App::logger()->error($e);
                throw $e;
            }

            $formData = (array)$request->request->get('order');
            if (!(bool)$formData) {
                $e = new \Exception\NotFoundException(sprintf('В POST запросе %s не содержится данных о заказе в один клик', json_encode($request->request->all())));
                \App::logger()->error($e);
                throw $e;
            }

            $formData = array_merge(array(
                'product_quantity'       => 0,
                'delivered_at'           => null,
                'recipient_first_name'   => null,
                'recipient_phonenumbers' => null,
                'recipient_scCard'       => null,
                'shop_id'                => null,
            ), $formData);

            $productQuantity = (int)$formData['product_quantity'];
            $product = \RepositoryManager::product()->getEntityByToken($productToken);
            if (!$product) {
                $e = new \Exception\NotFoundException(sprintf('Товар с токеном %s не найден в ядре', $productToken));
                \App::logger()->error($e);
                throw $e;
            }

            $productsInCart = array();
            if ((bool)$product->getKit()) {
                foreach($product->getKit() as $kit) {
                    $productsInCart[] = array('id' => $kit->getId(), 'quantity' => ($kit->getCount() * $productQuantity));
                }
            } else {
                $productsInCart = array(array('id' => $product->getId(), 'quantity' => $productQuantity));
            }

            $deliveryType = \RepositoryManager::deliveryType()->getEntityByToken(
                $formData['shop_id']
                ? \Model\DeliveryType\Entity::TYPE_SELF
                : \Model\DeliveryType\Entity::TYPE_STANDART
            );
            if (!$deliveryType) {
                throw new \Exception('Не определен метод доставки для заказа в один клик');
            }

            // чистим номер мобильного телефона
            $phone = trim((string)$formData['recipient_phonenumbers']);
            $phone = trim((string)$phone);
            $phone = preg_replace('/^\+7/', '8', $phone);
            $phone = preg_replace('/[^\d]/', '', $phone);
            if (10 == strlen($phone)) {
                $phone = '8' . $phone;
            }
            $formData['recipient_phonenumbers'] = $phone;

            $data = array(
                'geo_id'                    => \App::user()->getRegion()->getId(),
                'type_id'                   => \Model\Order\Entity::TYPE_1CLICK,
                'delivery_type_id'          => $deliveryType->getId(),
                'payment_id'                => 1, // оплата наличными
                'delivery_date'             => (string)$formData['delivered_at'],
                'first_name'                => (string)$formData['recipient_first_name'],
                'mobile'                    => trim((string)$formData['recipient_phonenumbers']),
                'svyaznoy_club_card_number' => str_replace(' ','', (string)$formData['recipient_scCard']),
                'product'                   => $productsInCart,
                'extra'                     => 'Это быстрый заказ за 1 клик. Уточните параметры заказа у клиента.',
                'ip'                        => $request->getClientIp(),
            );
            if ($formData['shop_id']) {
                $data['shop_id'] = (int)$formData['shop_id'];
            }

            try {
                $params = array();
                if ($userEntity && $userEntity->getToken()) {
                    $params['token'] = $userEntity->getToken();
                }
                $result = \App::coreClientV2()->query('order/create', $params, $data);
                $orderNumber = !empty($result['number']) ? (string)$result['number'] : null;
                if (!$orderNumber) {
                    throw new \Exception(sprintf('Не получен номер заказа. Ответ ядра: %s', json_encode($result)));
                }

                $order = \RepositoryManager::order()->getEntityByNumberAndPhone($orderNumber, $formData['recipient_phonenumbers']);
                if (!$order) {
                    \App::logger()->error(sprintf('Заказ №%s не найден в ядре', $result['number']));
                    $order = new \Model\Order\Entity(array('number' => $result['number']));
                }
            } catch (\Exception $e) {
                \App::logger()->warn($e);
                \App::exception()->remove($e);

                return new \Http\JsonResponse(array(
                    'success' => false,
                    'message' => 'Не удалось создать заказ.' . (735 == $e->getCode() ? ' Невалидный номер карты Связного клуба' : ''),
                ));
            }

            $orderData = json_encode(array (
                'order_article'    => implode(',', array_map(function($orderProduct) { /** @var $orderProduct \Model\Order\Product\Entity */ return $orderProduct->getId(); }, $order->getProduct())),
                'order_id'         => $order->getNumber(),
                'order_total'      => $order->getSum(),
                'product_quantity' => implode(',', array_map(function($orderProduct) { /** @var $orderProduct \Model\Order\Product\Entity */ return $orderProduct->getQuantity(); }, $order->getProduct())),
            ));

            $myThingsOrderData = array(
                'EventType' => 'MyThings.Event.Conversion',
                'Action' => '9902',
                'TransactionReference' => $order->getNumber(),
                'TransactionAmount' => str_replace(',', '.', $order->getSum()), // Полная сумма заказа (дроби через точку
                'Products' => array_map(function($orderProduct){
                    /** @var $orderProduct \Model\Order\Product\Entity  */
                    return array('id' => $orderProduct->getId(), 'price' => $orderProduct->getPrice(), 'qty' => $orderProduct->getQuantity());
                }, $order->getProduct()),
            );

            $shop = null;
            if ($order->getShopId()) {
                try {
                    $shop = \RepositoryManager::shop()->getEntityById($order->getShopId());
                } catch(\Exception $e) {
                    \App::logger()->error($e);
                }
            }

            $orderProducts = $order->getProduct();
            /** @var $orderProduct \Model\Order\Product\Entity */
            $orderProduct = reset($orderProducts);
            try {
                $product = $orderProduct ? \RepositoryManager::product()->getEntityById($orderProduct->getId()) : null;
            } catch (\Exception $e) {
                \App::logger()->error($e);
                $product = null;
            }

            return new \Http\JsonResponse(array(
                'success' => true,
                'message' => 'Заказ успешно создан',
                'data'    => array(
                    'title'   => 'Ваш заказ принят, спасибо!',
                    'content' => \App::templating()->render('order/_oneClick', array(
                        'page'              => new \View\Layout(),
                        'order'             => $order,
                        'orderData'         => $orderData,
                        'myThingsOrderData' => $myThingsOrderData,
                        'shop'              => $shop,
                        'orderProduct'      => $orderProduct,
                        'product'           => $product,
                    )),
                    'shop'    => $shop,
                ),
            ));
        } catch(\InvalidArgumentException $e){
            return new \Http\JsonResponse(array(
                'success' => false,
                'message' => 'Не удалось создать заказ' . (\App::config()->debug ? ('. ' . $e) : ''),
            ));
        }
    }
}
