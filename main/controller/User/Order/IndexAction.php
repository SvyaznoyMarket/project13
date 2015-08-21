<?php


namespace Controller\User\Order;

use EnterApplication\CurlTrait;
use EnterQuery as Query;

class IndexAction extends \Controller\User\PrivateAction {
    use CurlTrait;

    public function execute(\Http\Request $request) {
        $curl = $this->getCurl();

        $region = \App::user()->getRegion();

        $orderQuery = new Query\Order\GetByUserToken();
        $orderQuery->userToken = \App::user()->getEntity()->getToken();
        $orderQuery->offset = 0;
        $orderQuery->limit = 40;
        $orderQuery->prepare();

        $curl->execute();

        if ($error = $orderQuery->error) {
            throw $error;
        }

        // общее количество заказов
        $orderCount = $orderQuery->response->count;

        /** @var \Model\User\Order\Entity[] $orders */
        $orders = [];
        foreach ($orderQuery->response->orders as $item) {
            $orders[] = new \Model\User\Order\Entity($item);
        }

        /** @var \Model\User\Order\Entity[] $ordersByYear */
        $ordersByYear = [];
        foreach ($orders as $order) {
            $year = (int)$order->getCreatedAt()->format('Y');
            $ordersByYear[$year][] = $order;
        }

        /** @var \Model\Product\Entity[] $productsById */
        $productsById = [];
        $productIds = call_user_func(function() use (&$orders) {
            $ids = [];
            foreach ($orders as $order) {
                foreach ($order->product as $product) {
                    $ids[$product->getId()] = null;
                }
            }

            return array_keys($ids);
        });

        /** @var Query\Product\GetByIdListV3[] $productQueries */
        $productQueries = [];
        foreach (array_chunk($productIds, \App::config()->coreV2['chunk_size']) as $idsInChunk) {
            $productQuery = new Query\Product\GetByIdListV3();
            $productQuery->regionId = $region->getId();
            $productQuery->ids = $idsInChunk;
            $productQuery->prepare();
            $productQueries[] = $productQuery;
        }

        $curl->execute();

        foreach ($productQueries as $productQuery) {
            foreach ($productQuery->response->products as $item) {
                if (!@$item['id']) continue;
                $product = new \Model\Product\Entity($item);
                $productsById[$product->getId()] = $product;
            }
        }

        $page = new \View\User\Order\IndexPage();
        $page->setParam('ordersByYear', $ordersByYear);
        $page->setParam('productsById', $productsById);
        $page->setParam('orderCount', $orderCount);

        return new \Http\Response($page->show());
    }
}