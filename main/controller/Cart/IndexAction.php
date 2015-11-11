<?php

namespace Controller\Cart;

use Session\AbTest\ABHelperTrait;
use Model\ClosedSale\ClosedSaleEntity;

class IndexAction {
    use ABHelperTrait;

    /**
     * @param \Http\Request $request
     * @return \Http\Response
     */
    public function execute(\Http\Request $request) {
        //\App::logger()->debug('Exec ' . __METHOD__);

        $client = \App::coreClientV2();
        $user = \App::user();
        $cart = $user->getCart();

        $orderChannel = is_string($request->query->get('channel')) ? trim($request->query->get('channel')) : null; // SITE-6071
        if ($orderChannel) {
            \App::session()->set(\App::config()->order['channelSessionKey'], $orderChannel);
        }

        $orderWithCart = self::isOrderWithCart();

        if ($orderWithCart) {
            \App::session()->remove(\App::config()->order['splitSessionKey']);
        }

        // подготовка 1-го пакета запросов

        // запрашиваем пользователя, если он авторизован
        /*if ($user->getToken()) {
            \RepositoryManager::region()->prepareEntityById($user->getRegionId(), function($data) {
                $data = reset($data);
                if ((bool)$data) {
                    \App::user()->setRegion(new \Model\Region\Entity($data));
                }
            });
        }*/

        $sales = array_map(
            function (array $data) {
                return new ClosedSaleEntity($data);
            },
            \App::scmsClient()->query('api/promo-sale/get', [], [])
        );
        $sales = array_slice($sales, 0, 3);

        // запрашиваем текущий регион, если есть кука региона
        if ($user->getRegionId()) {
            \RepositoryManager::region()->prepareEntityById($user->getRegionId(), function($data) {
                $data = reset($data);
                if ($data) {
                    \App::user()->setRegion(new \Model\Region\Entity($data));
                }
            });
            
            $client->execute();
        }

        $updateResultProducts = $cart->update([], true);

        $page = $orderWithCart ? new \View\OrderV3\CartPage() : new \View\Cart\IndexPage();
        $page->setParam('sales', $sales);
        $page->setParam('orderUrl', \App::router()->generate($orderWithCart ? 'orderV3.delivery' : 'order'));
        $page->setParam('selectCredit', 1 == $request->cookies->get('credit_on'));
        $page->setParam('cartProductsById', array_reverse($cart->getProductsById(), true));
        $page->setParam('products', array_values(array_filter(array_map(function(\Session\Cart\Update\Result\Product $updateResultProduct) {
            if ($updateResultProduct->setAction === 'delete') {
                return;
            } else {
                return $updateResultProduct->fullProduct;
            }
        }, $updateResultProducts))));

        return new \Http\Response($page->show());
    }
}