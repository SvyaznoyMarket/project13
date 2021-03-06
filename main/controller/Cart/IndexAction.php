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
        }

        /** @var \Model\Config\Entity[] $configParameters */
        $configParameters = [];
        $callbackPhrases = [];
        \RepositoryManager::config()->prepare(['site_call_phrases'], $configParameters, function(\Model\Config\Entity $entity) use (&$category, &$callbackPhrases) {
            if ('site_call_phrases' === $entity->name) {
                $callbackPhrases = !empty($entity->value['cart']) ? $entity->value['cart'] : [];
            }

            return true;
        });

        $client->execute();

        // собираем статистику для RichRelevance
        try {
            if (\App::config()->product['pushRecommendation']) {
                \App::richRelevanceClient()->query('recsForPlacements', [
                    'placements'    => 'cart_page',
                    'productId'    => implode('|', array_keys($cart->getProductsById()))
                ]);
            }
        } catch (\Exception $e) {
            \App::exception()->remove($e);
        }

        $updateResultProducts = $cart->update([], true);

        $page = new \View\Cart\IndexPage();
        $page->setParam('sales', $sales);
        $page->setParam('orderUrl', \App::router()->generateUrl('orderV3'));
        $page->setParam('selectCredit', 1 == $request->cookies->get('credit_on'));
        $page->setParam('cartProductsById', array_reverse($cart->getProductsById(), true));
        $page->setParam('products', array_values(array_filter(array_map(function(\Session\Cart\Update\Result\Product $updateResultProduct) {
            if ($updateResultProduct->setAction === 'delete') {
                return;
            } else {
                return $updateResultProduct->fullProduct;
            }
        }, $updateResultProducts))));
        $page->setGlobalParam('callbackPhrases', $callbackPhrases);

        return new \Http\Response($page->show());
    }
}