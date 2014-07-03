<?php


namespace Controller\User;


class OrdersAction {

    public function __construct() {
        if (!\App::user()->getToken()) {
            throw new \Exception\AccessDeniedException();
        }
    }

    public function execute(\Http\Request $request) {

        if ($request->isXmlHttpRequest()) {
            return new \Http\JsonResponse([
                'data' => $this->getData($request)
            ]);
        }

        $data = $this->getData($request);

        $page = new \View\User\OrdersPage();
        $page->setParam('orders', $data['orders']);
        $page->setParam('orders_by_year', $data['orders_by_year']);
        $page->setParam('current_orders', $data['current_orders']);
        $page->setParam('products', $data['products']);
        $page->setParam('products_by_id', $data['products_by_id']);
        return new \Http\Response($page->show());

    }

    /** Возвращает массив заказов и продуктов
     * @param \Http\Request $request
     * @return array
     */
    public function getData(\Http\Request $request) {

        \App::logger()->debug('Exec ' . __METHOD__);

        $client = \App::coreClientV2();
        $user = \App::user();

        // подготовка 1-го пакета запросов

        /** @var $orders \Model\Order\Entity[] */
        $orders = [];
        $orders_by_year = [];

        \RepositoryManager::order()->prepareCollectionByUserToken($user->getToken(), function($data) use(&$orders, &$orders_by_year) {
            foreach ($data as $item) {
                $orders[] = new \Model\User\Order\Entity($item);
            }

            // сортировка по дате desc
            /** @var $orders \Model\User\Order\Entity[] */
            $orders = array_reverse($orders);

            // история заказов
            foreach ($orders as $order) {
                if ( !(bool) $order->getLifecycle() || // если данного поля не существует
                     $order->getLifecycle()[count($order->getLifecycle()) - 1]->getCompleted() // или последний статус true
                    )
                {
                    $orders_by_year[$order->getCreatedAt()->format('Y')][] = $order;
                }
            }
        });

        // выполнение 1-го пакета запросов
        $client->execute();

        $products = [];
        $products_by_id = [];
        $currentOrders = [];

        if ($orders) {

            // текущие заказы
            $currentOrders = array_filter($orders, function (\Model\User\Order\Entity $order) {
                $lifecycle = $order->getLifecycle();
                $lastcycle = end($lifecycle);
                return !$lastcycle->getCompleted();
            });

            $products = \RepositoryManager::product()->getCollectionById(
                array_map(function(\Model\User\Order\Entity $order) { return $order->getAllProductsIds(); }, $orders)
            );
            foreach ($products as $product) {
                $products_by_id[$product->getId()] = $product;
            }
        }

        return ['orders' => $orders, 'products' => $products, 'orders_by_year' => $orders_by_year, 'current_orders' => $currentOrders, 'products_by_id' => $products_by_id];

    }

} 