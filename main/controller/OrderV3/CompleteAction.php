<?php

namespace Controller\OrderV3;

use Model\Order\Entity;

class CompleteAction extends OrderV3 {

    /**
     * @param \Http\Request $request
     * @return \Http\Response
     * @throws \Exception
     */
    public function execute(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $orders = [];
        $ordersPayment = [];
        $products = [];

        // TODO remove previousSplit

        try {

            $sessionOrders = $this->session->get(\App::config()->order['sessionName'] ? : 'lastOrder');
            if (!(bool)$sessionOrders) throw new \Exception('В сессии нет заказов');

            // забираем заказы и доступные методы оплаты
            foreach ($sessionOrders as $sessionOrder) {

                // сами заказы
                $this->client->addQuery('order/get-by-mobile', ['number' => $sessionOrder['number'], 'mobile' => $sessionOrder['phone']], [], function ($data) use (&$orders, $sessionOrder) {
                    $data = reset($data);
                    $orders[$sessionOrder['number']] = $data ? new Entity($data) : null;
                });

                // методы оплаты для заказа
                $this->client->addQuery('payment-method/get-for-order', [
                    'geo_id'    => $this->user->getRegionId(),
                    'client_id' => 'site',
                    'number'    => $sessionOrder['number']
                ], [], function ($data) use ($sessionOrder, &$ordersPayment) {
                        $ordersPayment[$sessionOrder['number']] = $data;
                });
            }

            $this->client->execute();

            // получаем продукты для заказов

            foreach ($orders as $order) {
                /** @var $order \Model\Order\Entity */
                \RepositoryManager::product()->prepareCollectionById(array_map(function(\Model\Order\Product\Entity $product) { return $product->getId(); }, $order->getProduct()), null, function ($data) use ($order, &$products) {
                    foreach ($data as $productData) {
                        $products[$order->getNumber()][] = new \Model\Product\Entity($productData);
                    }
                } );
            }

            $this->client->execute();

        } catch (\Curl\Exception $e) {

        } catch (\Exception $e) {

        }

        $page = new \View\OrderV3\CompletePage();
        $page->setParam('orders', $orders);
        $page->setParam('ordersPayment', $ordersPayment);
        $page->setParam('products', $products);
        return new \Http\Response($page->show());
    }
}