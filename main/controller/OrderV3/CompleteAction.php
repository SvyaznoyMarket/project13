<?php

namespace Controller\OrderV3;

use Model\Order\Entity;
use Model\PaymentMethod\PaymentEntity;

class CompleteAction extends OrderV3 {

    /**
     * @param \Http\Request $request
     * @return \Http\Response
     * @throws \Exception
     */
    public function execute(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        /** @var \Model\Order\Entity $orders */
        $orders = [];
        /** @var \Model\PaymentMethod\PaymentEntity[] $ordersPayment */
        $ordersPayment = [];
        $products = [];
        $paymentProviders = [];
        $privateClient = \App::coreClientPrivate();
//        $shops = [];

        try {

            $sessionOrders = $this->session->get(\App::config()->order['sessionName'] ? : 'lastOrder');
            if (!(bool)$sessionOrders) throw new \Exception('В сессии нет заказов');

            // забираем заказы и доступные методы оплаты
            foreach ($sessionOrders as $sessionOrder) {

                if (!is_array($sessionOrder)) continue;

                // сами заказы
                $this->client->addQuery('order/get-by-mobile', ['number' => $sessionOrder['number'], 'mobile' => $sessionOrder['phone']], [], function ($data) use (&$orders, $sessionOrder) {
                    $data = reset($data);
                    $orders[$sessionOrder['number']] = $data ? new Entity($data) : null;
                });

                // методы оплаты для заказа
                $this->client->addQuery('payment-method/get-for-order', [
                    'geo_id'    => $this->user->getRegionId(),
                    'client_id' => 'site',
                    'number_erp'    => $sessionOrder['number_erp']
                ], [], function ($data) use ($sessionOrder, &$ordersPayment) {
                        $ordersPayment[$sessionOrder['number']] = new PaymentEntity($data);
                });
            }

            unset($sessionOrder);

            $this->client->execute();

            // получаем продукты для заказов
            foreach ($orders as $order) {

                /** @var $order \Model\Order\Entity */
                \RepositoryManager::product()->prepareCollectionById(array_map(function(\Model\Order\Product\Entity $product) { return $product->getId(); }, $order->getProduct()), null, function ($data) use ($order, &$products) {
                    foreach ($data as $productData) {
                        $products[$productData['id']] = new \Model\Product\Entity($productData);
                    }
                } );

                $onlineMethodsId = array_map(function(\Model\PaymentMethod\PaymentMethod\PaymentMethodEntity $method) { return $method->id; },
                    array_filter($ordersPayment[$order->getNumber()]->methods,
                        function(\Model\PaymentMethod\PaymentMethod\PaymentMethodEntity $payment) {return $payment->paymentGroup->id == 2;}
                    )
                );

                if (!(bool)$onlineMethodsId) continue;

                // Онлайн-оплата для каждого заказа
                foreach (array_values($onlineMethodsId) as $methodId) {

                    $privateClient->addQuery('site-integration/payment-config',
                        [
                            'method_id' => $methodId,
                            'order_id'  => $order->getId(),
                        ],
                        [
                            'back_ref'    => \App::router()->generate('orderV3.complete', [], true),// обратная ссылка
                            'email'       => $order->getUser() ? $order->getUser()->getEmail() : '',
//                            'card_number' => $order->card,
                            'user_token'  => $request->cookies->get('UserTicket'),// токен кросс-авторизации. может быть передан для Связного-Клуба (UserTicket)
                        ],
                        function($data) use (&$paymentProviders, $order, $methodId) {
                            if ((bool)$data && isset($data['code']) && $data['code'] == 200) {
                                if (!isset($paymentProviders[$order->getNumber()])) $paymentProviders[$order->getNumber()] = [];
                                $paymentProviders[$order->getNumber()][$methodId] =  $data ;
                            }
                        },
                        function(\Exception $e) {
                            \App::exception()->remove($e);
                        }
                    );
                }

            }

            // получаем магазины
            /*\RepositoryManager::shop()->prepareCollectionById(array_map(function(\Model\Order\Entity $order){ return $order->getShopId(); }, array_filter($orders, function(\Model\Order\Entity $order){ return $order->getShopId() != 0; })),
                function($data) use (&$shops) {
                    foreach($data as $shopData) {
                        if (isset($shopData['id'])) $shops[$shopData['id']] = new \Model\Shop\Entity($shopData);
                    }
            });*/

            $this->client->execute();
            $privateClient->execute();
            unset($order, $methodId, $onlineMethodsId, $privateClient);

            // TODO фильтрация $ordersPayment по $paymentProviders
/*            foreach ($orders as $order) {
                $ordersPayment[$order->getNumber()]->methods = array_filter($ordersPayment[$order->getNumber()]->methods, function (\Model\PaymentMethod\PaymentMethod\PaymentMethodEntity $method) use ($paymentProviders, $order) {
                    return isset($paymentProviders[$order->getNumber()][$method->id]);
                });
            }*/

            // очищаем корзину от заказанных продуктов
            foreach ($products as $product) {
                if ($product instanceof \Model\Product\Entity) $this->cart->setProduct($product, 0);
            }
            unset($product);

            // логируем этот шит
            foreach ($orders as $order) {
                $productIds = array_map(function(\Model\Order\Product\Entity $product) { return $product->getId(); }, $order->getProduct());
                $productsForOrder = array_filter($products, function(\Model\Product\Entity $product) use ($productIds) { return in_array($product->getId(), $productIds); });
                $data = [];
                $data['order-number'] = $order->numberErp;
                $data['order-products'] = $productIds;
                $data['order-names'] = array_map(function(\Model\Product\Entity $product) { return $product->getName(); }, $productsForOrder);
                $data['order-product-category'] = array_map(function(\Model\Product\Entity $product) { $category = $product->getMainCategory(); return $category->getName(); }, $productsForOrder);
                $data['order-product-price'] = array_map(function(\Model\Product\Entity $product) { return $product->getPrice(); }, $productsForOrder);
                $data['order-sum'] = $order->getSum();
                $data['order-delivery-price'] = isset($order->getDelivery()[0]) ? $order->getDelivery()[0]->getPrice() : '';
                $data['user-phone'] = $order->getMobilePhone();
                $this->logger($data);
            }

            unset($order, $data, $productIds, $productsForOrder);

        } catch (\Curl\Exception $e) {
            // TODO
        } catch (\Exception $e) {
            // TODO
        }

        $page = new \View\OrderV3\CompletePage();
        $page->setParam('orders', $orders);
        $page->setParam('ordersPayment', $ordersPayment);
        $page->setParam('products', $products);
        $page->setParam('userEntity', $this->user->getEntity());
        $page->setParam('paymentProviders', $paymentProviders);

        $response = new \Http\Response($page->show());
        $response->headers->setCookie(new \Http\Cookie('enter_order_v3_wanna', 0, 0, '/order',\App::config()->session['cookie_domain'], false, false)); // кнопка "Хочу быстрее"
        return $response;
    }
}