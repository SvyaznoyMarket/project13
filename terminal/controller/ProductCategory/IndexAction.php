<?php

namespace Terminal\Controller\ProductCategory;

class IndexAction {
    /**
     * @param $categoryId
     * @param \Http\Request $request
     * @throws \Exception\NotFoundException
     * @return \Http\Response
     */
    public function execute($categoryId, \Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $category = \RepositoryManager::productCategory()->getEntityById($categoryId);
        if (!$category) {
            throw new \Exception\NotFoundException(sprintf('Категория #% не найдена', $category->getId()));
        }

        $productSorting = new \Model\Product\TerminalSorting();

        $page = new \Terminal\View\ProductCategory\IndexPage();
        $page->setParam('category', $category);
        $page->setParam('productSorting', $productSorting);

        return new \Http\Response($page->show());
    }

    /**
     * @param $categoryId
     * @param \Http\Request $request
     * @return \Http\JsonResponse
     * @throws \Exception\NotFoundException
     */
    public function product($categoryId, \Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $productSorting = new \Model\Product\TerminalSorting();

        $client = \App::coreClientV2();
        $user = \App::user();
        $region = \App::user()->getRegion();

        $sortData = (array)$request->get('sort');
        if ((bool)$sortData) {
            $sortName = key($sortData);
            $sortDirection = current($sortData);
            try {
                $productSorting->setActiveSort($sortName);
                $productSorting->setActiveDirection($sortDirection);
            } catch (\Exception $e) {
                \App::logger()->error($e);
            }
        }

        $filterData = (array)$request->get('f', []);
        $offset =  (int)$request->get('offset', 0);
        $limit = (int)$request->get('limit', 32);

        // запрашиваем категорию по id
        $category = null;
        \RepositoryManager::productCategory()->prepareCollectionById([$categoryId], $region, function($data) use (&$category) {
            if ((bool)$data) {
                $item = reset($data);
                $category = new \Model\Product\Category\Entity($item);
            }
        });

        // фильтры
        $filters = [];
        \RepositoryManager::productFilter()->prepareCollectionByCategory(new \Model\Product\Category\Entity(['id' => $categoryId]), $region,
            function($data) use (&$filters) {
                foreach ($data as $item) {
                    $filters[] = new \Model\Product\Filter\Entity($item);
                }
            },
            function (\Exception $e) use ($categoryId) {
                \App::exception()->remove($e);
                \App::logger()->error(sprintf('Не удалось получить фильтры для категории #%s', $categoryId));
            }
        );
        \App::coreClientV2()->execute();

        if (!$category) {
            throw new \Exception\NotFoundException(sprintf('Категория товара #%s не найдена.', $categoryId));
        }

        $productFilter = null;
        if ((bool)$filters) {
            $productFilter = new \Model\Product\Filter($filters);
            $productFilter->setCategory($category);
            $productFilter->setValues($filterData);
        }

        $response = [];
        $client->addQuery('listing/list', [
            'filter' => [
                'filters' => $productFilter ? $productFilter->dump() : [],
                'sort'    => $productSorting->dump(),
                'offset'  => $offset,
                'limit'   => $limit,
            ],
            'region_id' => $user->getRegion()->getId(),
        ], [], function($data) use(&$response) {
            $response = $data;
        });
        $client->execute(\App::config()->coreV2['retryTimeout']['medium']);

        /** @var $collection \Model\Product\TerminalEntity[] */
        $collection = [];
        $entityClass = '\Model\Product\TerminalEntity';
        if (!empty($response['list'])) {
            \RepositoryManager::product()->prepareCollectionById($response['list'], $user->getRegion(), function($data) use(&$collection, $entityClass) {
                foreach ($data as $item) {
                    $collection[] = new $entityClass($item);
                }
            });
        }
        $client->execute(\App::config()->coreV2['retryTimeout']['medium']);

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
