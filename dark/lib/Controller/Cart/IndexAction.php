<?php

namespace Controller\Cart;

class IndexAction {
    public function execute(\Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $client = \App::coreClientV2();
        $cart = \App::user()->getCart();

        $cartProductsById = $cart->getProducts();
        $cartServicesById = $cart->getServices();

        $productIds = array_keys($cartProductsById);
        $serviceIds = array_keys($cartServicesById);

        $products = array();
        $services = array();

        if ((bool)$productIds) {
            $client->addQuery('product/get', array(
                'select_type' => 'id',
                'id'          => $productIds,
                'geo_id'      => \App::user()->getRegion()->getId(),
            ), array(), function($data) use(&$products, $cartProductsById) {
                foreach ($data as $item) {
                    $products[] = new \Model\Product\CartEntity($item);
                }
            });
        }

        if ((bool)$serviceIds) {
            $client->addQuery('service/get2', array(
                'id'     => $serviceIds,
                'geo_id' => \App::user()->getRegion()->getId(),
            ), array(), function($data) use(&$services, $cartServicesById) {
                foreach ($data as $item) {
                    $services[] = new \Model\Product\Service\Entity($item);
                }
            });
        }

        if ((bool)$productIds || (bool)$serviceIds) {
            $client->execute();
        }

        $page = new \View\Cart\IndexPage();
        $page->setParam('selectCredit', 1 == $request->cookies->get('credit_on'));
        $page->setParam('products', $products);
        $page->setParam('services', $services);
        $page->setParam('cartProductsById', $cartProductsById);
        $page->setParam('cartServicesById', $cartServicesById);

        return new \Http\Response($page->show());
    }
}