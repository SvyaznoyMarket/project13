<?php

namespace Controller\Cart;

class IndexAction {
    /**
     * @param \Http\Request $request
     * @return \Http\Response
     */
    public function execute(\Http\Request $request) {
        //\App::logger()->debug('Exec ' . __METHOD__);

        $client = \App::coreClientV2();
        $user = \App::user();
        $cart = $user->getCart();

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

        // запрашиваем текущий регион, если есть кука региона
        if ($user->getRegionId()) {
            \RepositoryManager::region()->prepareEntityById($user->getRegionId(), function($data) {
                $data = reset($data);
                if ((bool)$data) {
                    \App::user()->setRegion(new \Model\Region\Entity($data));
                }
            });
            
            $client->execute();
        }

        $regionEntity = $user->getRegion();
        if ($regionEntity instanceof \Model\Region\Entity) {
            $user->setRegion($regionEntity);
        }

        $region = $user->getRegion();

        // подготовка 2-го пакета запросов
        $cartProductsById = array_reverse($cart->getProducts(), true);

        $productIds = array_keys($cartProductsById);

        /** @var \Model\Product\Entity[] $products */
        $products = [];
        $medias = [];

        // запрашиваем список товаров
        if ((bool)$productIds) {
            \RepositoryManager::product()->prepareCollectionById($productIds, $region, function($data) use(&$products, $cartProductsById) {
                foreach ($data as $item) {
                    $products[] = new \Model\Product\Entity($item);
                }
            });

            \RepositoryManager::product()->prepareProductsMediasByIds($productIds, $medias);
        }

        // выполнение 2-го пакета запросов
        $client->execute();

        \RepositoryManager::product()->setMediasForProducts($products, $medias);

        $page = new \View\Cart\IndexPage();
        $page->setParam('selectCredit', 1 == $request->cookies->get('credit_on'));
        $page->setParam('productEntities', $products);
        $page->setParam('products', $products);
        $page->setParam('cartProductsById', $cartProductsById);

        return new \Http\Response($page->show());
    }
}