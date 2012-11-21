<?php

namespace Controller\Product;

class IndexAction {
    public function execute($productPath, \Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $productToken = explode('/', $productPath);
        $productToken = end($productToken);

        $repository = \RepositoryManager::getProduct();
        $product = $repository->getEntityByToken($productToken);
        if (!$product) {
            throw new \Exception\NotFoundException(sprintf('Товар с токеном "%s" не найден.', $productToken));
        }

        if ($product->getConnectedProductsViewMode() == $product::DEFAULT_CONNECTED_PRODUCTS_VIEW_MODE) {
            $showRelatedUpper = false;
        } else {
            $showRelatedUpper = true;
        }

        $accessoriesId =  array_slice($product->getAccessoryId(), 0, \App::config()->product['itemsInSlider'] * 2);
        $relatedId = array_slice($product->getRelatedId(), 0, \App::config()->product['itemsInSlider'] * 2);
        $partsId = array();

        foreach ($product->getKit() as $part) {
            $partsId[] = $part->getId();
        }

        $accessories = array_flip($accessoriesId);
        $related = array_flip($relatedId);
        $kit = array_flip($partsId);

        if ((bool)$accessoriesId || (bool)$relatedId || (bool)$partsId) {
            try {
                $products = $repository->getCollectionById(array_merge($accessoriesId, $relatedId, $partsId));
            } catch (\Exception $e) {
                \App::$exception = $e;
                \App::logger()->error($e);

                $products = array();
                $accessories = array();
                $related = array();
                $kit = array();
            }

            foreach ($products as $item) {
                if (isset($accessories[$item->getId()])) $accessories[$item->getId()] = $item;
                if (isset($related[$item->getId()])) $related[$item->getId()] = $item;
                if (isset($kit[$item->getId()])) $kit[$item->getId()] = $item;
            }
        }
        $dataForCredit = $this->getDataForCredit($product);

        $showroomShops = array();
        //загружаем магазины, если товар доступен только на витрине
        if (!$product->getIsBuyable() && $product->getState()->getIsShop()) {
            $shopIds = array();
            foreach ($product->getStock() as $stock) {
                $quantityShowroom = $stock->getQuantityShowroom();
                $shopId = $stock->getShopId();
                if (!empty($quantityShowroom) && !empty($shopId)) {
                    $shopIds[] = $shopId;
                }
            }
            if (count($shopIds)) {
                try {
                    $showroomShops = \RepositoryManager::getShop()->getCollectionById($shopIds);
                } catch (\Exception $e) {
                    \App::$exception = $e;
                    \App::logger()->error($e);

                    $showroomShops = array();
                }

            }
        }

        $page = new \View\Product\IndexPage();
        $page->setParam('product', $product);
        $page->setParam('title', $product->getName());
        $page->setParam('showRelatedUpper', $showRelatedUpper);
        $page->setParam('showAccessoryUpper', !$showRelatedUpper);
        $page->setParam('accessories', $accessories);
        $page->setParam('related', $related);
        $page->setParam('kit', $kit);
        $page->setParam('dataForCredit', $dataForCredit);
        $page->setParam('showroomShops', $showroomShops);

        return new \Http\Response($page->show());
    }

    /**
     * Собирает в массив данные, необходимые для плагина online кредитовария // скопировано из symfony
     *
     * @param $product
     * @return array
     */
    private function getDataForCredit(\Model\Product\Entity $product) {
        $result = array();

        $category = $product->getMainCategory();
        $cart = \App::user()->getCart();
        try {
            $productType = $category ? \RepositoryManager::getCreditBank()->getCreditTypeByCategoryToken($category->getToken()) : '';
        } catch (\Exception $e) {
            \App::$exception = $e;
            \App::logger()->error($e);

            $productType = '';
        }

        $dataForCredit = array(
            'price'        => $product->getPrice(),
            //'articul'      => $product->getArticle(),
            'name'         => $product->getName(),
            'count'        => $cart->getQuantityByProduct($product->getId()),
            'product_type' => $productType,
            'session_id'   => session_id()
        );
        $result['creditIsAllowed'] = (bool) (($product->getPrice() * (($cart->getQuantityByProduct($product->getId()) > 0)? $cart->getQuantityByProduct($product->getId()) : 1)) > \App::config()->product['minCreditPrice']);
        $result['creditData'] = json_encode($dataForCredit);

        return $result;
    }
}