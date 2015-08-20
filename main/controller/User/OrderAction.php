<?php

namespace Controller\User;

class OrderAction extends PrivateAction {

    public function execute(\Http\Request $request, $orderId) {
        //\App::logger()->debug('Exec ' . __METHOD__);

        try {
            $data = $this->getData($request, $orderId);
        } catch (\Curl\Exception $e) {
            \App::exception()->remove($e);
            $page = new \View\Error\IndexPage();
            $page->setParam('message', $e->getMessage());
            return new \Http\Response($page->show());
        } catch (\Exception $e) {
            //\App::exception()->remove($e);
            $page = new \View\Error\IndexPage();
            $page->setParam('message', $e->getMessage());
            return new \Http\Response($page->show());
        }

        $page = new \View\User\OrderPage();
        $page->setParam('order', $data['order']);
        $page->setParam('products', $data['products']);
        $page->setParam('delivery', $data['delivery']);
        $page->setParam('current_orders_count', $data['current_orders_count']);
        $page->setParam('shop', $data['shop']);

        return new \Http\Response($page->show());
    }

    private function getData(\Http\Request $request, $orderId) {

        //\App::logger()->debug('Exec ' . __METHOD__);

        $client = \App::coreClientV2();
        $user = \App::user();

        // подготовка 1-го пакета запросов (заказы)

        /** @var $order \Model\User\Order\Entity */
        $order = null;
        $currentOrdersCount = 0;

        \RepositoryManager::order()->prepareCollectionByUserToken(
            $user->getToken(),
            function($data) use(&$order, &$orderId, &$currentOrdersCount) {
                if (empty($data['orders'][0])) return;
                foreach ($data['orders'] as $item) {
                    if (empty($item['id'])) continue;

                    $orderItem = new \Model\User\Order\Entity($item);
                    if ($orderId == $item['id']) $order = $orderItem;
                    if (!$orderItem->isCompleted()) $currentOrdersCount++;
                }
            },
            0,
            40
        );

        // выполнение 1-го пакета запросов
        $client->execute();

        if (!$order) throw new \Exception('Не найден заказ #'.$orderId);

        // подготовка 2-го пакета запросов (продукты)
        /** @var \Model\Product\Entity[] $products */
        $products = array_map(function($productId) { return new \Model\Product\Entity(['id' => $productId]); }, $order->getAllProductsIds());

        \RepositoryManager::product()->prepareProductQueries($products, 'media category');

        $delivery = $order->getDelivery() ? \RepositoryManager::deliveryType()->getEntityById($order->getDelivery()->getTypeId()) : null;

        // если не удалось получить доставку через одно значение, то попробуем через другое
        if ($delivery == null) {
            $delivery = \RepositoryManager::deliveryType()->getEntityById($order->getDeliveryTypeId());
        }

        $shop = null;
        if ($delivery && in_array($delivery->getToken(), ['now', 'self']) && !is_null($order->getShopId())) {
            \RepositoryManager::shop()->prepareCollectionById(
                [$order->getShopId()],
                function ($data) use (&$shop) {
                    if (isset($data[0])) $shop = new \Model\Shop\Entity($data[0]);
                }
            );
        }

        // выполнение 2-го пакета запросов
        $client->execute();

        return ['order' => $order, 'products' => $products, 'delivery' => $delivery, 'shop' => $shop, 'current_orders_count' => $currentOrdersCount];

    }
}