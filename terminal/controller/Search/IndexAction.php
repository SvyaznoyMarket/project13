<?php

namespace Terminal\Controller\Search;

class IndexAction {
    /**
     * @param string $searchQuery
     * @param \Http\Request $request
     * @throws \Exception\NotFoundException
     * @return \Http\Response
     */
    public function execute($searchQuery, \Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $searchQuery = trim((string)$searchQuery);
        if (empty($searchQuery)) {
            throw new \Exception\NotFoundException(sprintf('Пустая фраза поиска.', $searchQuery));
        }

        $page = new \Terminal\View\Search\IndexPage();
        $page->setParam('searchQuery', $searchQuery);

        return new \Http\Response($page->show());
    }

    public function product($searchQuery, \Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $client = \App::coreClientV2();
        $user = \App::user();

        $searchQuery = trim((string)$searchQuery);
        if (empty($searchQuery)) {
            throw new \Exception\NotFoundException(sprintf('Пустая фраза поиска.', $searchQuery));
        }

        $categoryId = null; // TODO брать параметр из запроса

        $offset =  (int)$request->get('offset', 0);
        $limit = (int)$request->get('limit', 32);

        // параметры ядерного запроса
        $params = array(
            'request'  => $searchQuery,
            'geo_id'   => \App::user()->getRegion()->getId(),
            'start'    => $offset,
            'limit'    => $limit,
            'use_mean' => true,
        );
        if ($categoryId) {
            $params['product_category_id'] = $categoryId;
        } else {
            //$params['is_product_category_first_only'] = false;
        }
        // ядерный запрос
        $result = [];
        \App::coreClientV2()->addQuery('search/get', $params, [], function ($data) use (&$result) {
            $result = $data;
        });
        \App::coreClientV2()->execute(\App::config()->coreV2['retryTimeout']['huge']);

        $productResult = (isset($result[1]) && !empty($result[1]['data'])) ? $result[1] : null;
        if (!$productResult) {
            return new \Http\JsonResponse([
                'success'  => false,
                'products' => [],
            ]);
        }

        /** @var $collection \Model\Product\TerminalEntity[] */
        $collection = [];
        $entityClass = '\Model\Product\TerminalEntity';
        \RepositoryManager::product()->prepareCollectionById($productResult['data'], $user->getRegion(), function($data) use(&$collection, $entityClass) {
            foreach ($data as $item) {
                $collection[] = new $entityClass($item);
            }
        });
        $client->execute();

        $shopId = \App::config()->region['shop_id'];
        $productData = [];
        foreach ($collection as $product) {
            $productData[] = [
                'id'            => $product->getId(),
                'name'          => $product->getName(),
                'image'         => $product->getImageUrl(3),
                'article'       => $product->getArticle(),
                'price'         => $product->getPrice(),
                'description'   => $product->getTagline(),
                'isBuyable'     => $product->getIsBuyable($shopId),
                'isInShop'      => $product->getIsInShop($shopId),
                'isInShowroom'  => $product->getIsInShowroom($shopId),
                'isInStore'     => $product->getState()->getIsStore(),
                'hasSupplier'   => $product->getState()->getIsSupplier(),
                'isInOtherShop' => $product->getState()->getIsShop(), //
                'line'          => $product->getLine() ? [
                    'id'              =>  $product->getLine()->getId(),
                    'token'           =>  $product->getLine()->getToken(),
                    'name'            =>  $product->getLine()->getName(),
                    'image'           =>  $product->getLine()->getImage(),
                    'kitQuantity'     =>  $product->getLine()->getKitCount(),
                    'productQuantity' =>  $product->getLine()->getProductCount(),
                    'totalQuantity'   =>  $product->getLine()->getTotalCount(),
                ] : null,
                'partQuantity'  => count($product->getKit()),
            ];
        }

        return new \Http\JsonResponse([
            'success'  => true,
            'products' => $productData,
        ]);
    }
}
