<?php


namespace Controller\User\Order;

use EnterApplication\CurlTrait;
use EnterQuery as Query;

class IndexAction extends \Controller\User\PrivateAction {
    use CurlTrait;

    public function execute(\Http\Request $request) {
        $curl = $this->getCurl();
        $productRepository = \RepositoryManager::product();

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
        $pointUis = [];
        $orderNumberErps = [];
        foreach ($orderQuery->response->orders as $item) {
            $order = new \Model\User\Order\Entity($item);
            if (!$order->numberErp) continue;

            $orders[] = $order;

            $orderNumberErps[] = $order->numberErp;

            if ($order->pointUi) {
                $pointUis[$order->pointUi] = null;
            }
        }
        $pointUis = array_keys($pointUis);

        /** @var \Model\User\Order\Entity[] $ordersByYear */
        $ordersByYear = [];
        foreach ($orders as $order) {
            $year = (int)$order->getCreatedAt()->format('Y');
            $ordersByYear[$year][] = $order;
        }

        $productIds = call_user_func(function() use (&$orders) {
            $ids = [];
            foreach ($orders as $order) {
                foreach ($order->product as $product) {
                    $ids[$product->getId()] = null;
                }
            }

            return array_keys($ids);
        });

        /** @var Query\Product\GetByIdList[] $productQueries */
        $productQueries = [];
        /** @var Query\Product\GetDescriptionByIdList[] $productDescriptionQueries */
        $productDescriptionQueries = [];
        foreach (array_chunk($productIds, \App::config()->coreV2['chunk_size']) as $idsInChunk) {
            $productQuery = new Query\Product\GetByIdList();
            $productQuery->regionId = $region->getId();
            $productQuery->ids = $idsInChunk;
            $productQuery->prepare();
            $productQueries[] = $productQuery;

            $productDescriptionQuery = new Query\Product\GetDescriptionByIdList();
            $productDescriptionQuery->ids = $idsInChunk;
            $productDescriptionQuery->prepare();
            $productDescriptionQueries[] = $productDescriptionQuery;
        }

        /** @var Query\Product\GetByIdList[] $viewedProductQueries */
        $viewedProductQueries = [];
        /** @var Query\Product\GetDescriptionByIdList[] $viewedProductDescriptionQueries */
        $viewedProductDescriptionQueries = [];
        foreach (array_chunk($productRepository->getViewedProductIdsByHttpRequest($request), \App::config()->coreV2['chunk_size']) as $idsInChunk) {
            $viewedProductQuery = new Query\Product\GetByIdList();
            $viewedProductQuery->regionId = $region->getId();
            $viewedProductQuery->ids = $idsInChunk;
            $viewedProductQuery->prepare();
            $viewedProductQueries[] = $viewedProductQuery;

            $viewedProductDescriptionQuery = new Query\Product\GetDescriptionByIdList();
            $viewedProductDescriptionQuery->ids = $idsInChunk;
            $viewedProductDescriptionQuery->filter->media = true;
            $viewedProductDescriptionQuery->prepare();
            $viewedProductDescriptionQueries[] = $viewedProductDescriptionQuery;
        }

        /** @var Query\Point\GetByUiList[] $pointQueries */
        $pointQueries = [];
        foreach (array_chunk($pointUis, \App::config()->coreV2['chunk_size']) as $uisInChunk) {
            $pointQuery = new Query\Point\GetByUiList();
            $pointQuery->uis = $uisInChunk;
            $pointQuery->prepare();
            $pointQueries[] = $pointQuery;
        }

        /** @var Query\PaymentMethod\GetByOrderNumberErp[] $paymentMethodQueries */
        $paymentMethodQueries = [];
        foreach (array_chunk($orderNumberErps, 4) as $numbersInChunk) {
            $paymentMethodQuery = new Query\PaymentMethod\GetByOrderNumberErp();
            $paymentMethodQuery->regionId = $region->getId();
            $paymentMethodQuery->numberErps = $numbersInChunk;
            $paymentMethodQuery->noDiscount = true;
            $paymentMethodQuery->prepare();
            $paymentMethodQueries[] = $paymentMethodQuery;
        }

        $curl->execute();

        $productDescriptionDataById = [];
        foreach ($productDescriptionQueries as $productDescriptionQuery) {
            foreach ($productDescriptionQuery->response->products as $item) {
                if (@$item['core_id']) {
                    $productDescriptionDataById[$item['core_id']] = $item;
                }
            }
        }

        /** @var \Model\Product\Entity[] $productsById */
        $productsById = [];
        foreach ($productQueries as $productQuery) {
            foreach ($productQuery->response->products as $item) {
                if (!@$item['id'] || !isset($productDescriptionDataById[$item['id']])) continue;
                $product = new \Model\Product\Entity($item);
                $product->importFromScms($productDescriptionDataById[$item['id']]);
                $productsById[$product->getId()] = $product;
            }
        }

        $viewedProductDescriptionDataById = [];
        foreach ($viewedProductDescriptionQueries as $viewedProductDescriptionQuery) {
            foreach ($viewedProductDescriptionQuery->response->products as $item) {
                if (@$item['core_id']) {
                    $viewedProductDescriptionDataById[$item['core_id']] = $item;
                }
            }
        }

        /** @var \Model\Product\Entity[] $viewedProductsById */
        $viewedProductsById = [];
        foreach ($viewedProductQueries as $viewedProductQuery) {
            foreach ($viewedProductQuery->response->products as $item) {
                if (!@$item['id'] || !isset($viewedProductDescriptionDataById[$item['id']])) continue;
                $product = new \Model\Product\Entity($item);
                $product->importFromScms($viewedProductDescriptionDataById[$item['id']]);
                $viewedProductsById[$product->getId()] = $product;
            }
        }

        /** @var \Model\Point\PointEntity[] $pointsByUi */
        $pointsByUi = [];
        foreach ($pointQueries as $pointQuery) {
            foreach ($pointQuery->response->points as $item) {
                if (!@$item['ui']) continue;
                $point = new \Model\Point\PointEntity($item);
                $pointsByUi[$point->ui] = $point;
            }
        }

        /** @var array */
        $onlinePaymentAvailableByNumberErp = [];
        foreach ($paymentMethodQueries as $paymentMethodQuery) {
            foreach ($paymentMethodQuery->response->paymentMethodsByOrderNumberErp as $numberErp => $items) {
                foreach ($items as $item) {
                    if (5 == $item['id']) {
                        $onlinePaymentAvailableByNumberErp[$numberErp] = true;
                        break;
                    }
                }
            }
        }

        $page = new \View\User\Order\IndexPage();
        $page->setParam('ordersByYear', $ordersByYear);
        $page->setParam('productsById', $productsById);
        $page->setParam('orderCount', $orderCount);
        $page->setParam('pointsByUi', $pointsByUi);
        $page->setParam('viewedProducts', array_values($viewedProductsById));
        $page->setParam('onlinePaymentAvailableByNumberErp', $onlinePaymentAvailableByNumberErp);

        return new \Http\Response($page->show());
    }
}