<?php

namespace Controller\User\Order;

use EnterApplication\CurlTrait;
use EnterQuery as Query;

class ShowAction extends \Controller\User\PrivateAction {
    use CurlTrait;

    public function execute(\Http\Request $request, $orderId) {
        //\App::logger()->debug('Exec ' . __METHOD__);

        $client = \App::coreClientV2();

        $userToken = \App::user()->getEntity()->getToken();

        $orderQuery = new Query\Order\GetById();
        $orderQuery->userToken = $userToken;
        $orderQuery->id = $orderId;
        $orderQuery->prepare();

        $orderCountQuery = new Query\Order\GetByUserToken();
        $orderCountQuery->userToken = $userToken;
        $orderCountQuery->offset = 0;
        $orderCountQuery->limit = 0;
        $orderCountQuery->prepare();

        $this->getCurl()->execute();

        if ($error = $orderQuery->error) {
            throw $error;
        }

        try {
            if (!$orderQuery->response->order) {
                throw new \Exception('Заказ не найден');
            }

            $order = new \Model\User\Order\Entity($orderQuery->response->order);
            $currentCount = $orderCountQuery->response->currentCount;

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
            if ($delivery && in_array($delivery->getToken(), ['now', 'self'], true) && !is_null($order->getShopId())) {
                \RepositoryManager::shop()->prepareCollectionById(
                    [$order->getShopId()],
                    function ($data) use (&$shop) {
                        if (isset($data[0])) $shop = new \Model\Shop\Entity($data[0]);
                    }
                );
            }

            // выполнение 2-го пакета запросов
            $client->execute();

        } catch (\Exception $e) {
            \App::exception()->remove($e);
            $page = new \View\Error\IndexPage();
            $page->setParam('message', $e->getMessage());

            return new \Http\Response($page->show());
        }

        $page = new \View\User\OrderPage();
        $page->setParam('order', $order);
        $page->setParam('products', $products);
        $page->setParam('delivery', $delivery);
        $page->setParam('current_orders_count', $currentCount);
        $page->setParam('shop', $shop);

        return new \Http\Response($page->show());
    }
}