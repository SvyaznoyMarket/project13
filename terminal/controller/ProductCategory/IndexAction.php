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

        $filterData = (array)$request->get('filter', []);
        $offset =  (int)$request->get('offset', 0);
        $limit = (int)$request->get('limit', 32);

        // запрашиваем категорию по id
        $category = \RepositoryManager::productCategory()->getEntityById($categoryId);

        if (!$category) {
            throw new \Exception\NotFoundException(sprintf('Категория товара #%s не найдена.', $categoryId));
        }

        $productFilter = new \Model\Product\Filter($filterData, false);
        $productFilter->setCategory($category);

        $response = array();
        $client->addQuery('listing/list', array(
            'filter' => array(
                'filters' => $productFilter->dump(),
                'sort'    => $productSorting->dump(),
                'offset'  => $offset,
                'limit'   => $limit,
            ),
            'region_id' => $user->getRegion()->getId(),
        ), array(), function($data) use(&$response) {
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
                'description'   => $product->getDescription(),
                'isBuyable'     => $product->getIsBuyable($shopId),
                'isInShop'      => $product->getIsInShop($shopId),
                'isInShowroom'  => $product->getIsInShowroom($shopId),
                'isInStore'     => $product->getState()->getIsStore(),
                'hasSupplier'   => $product->getState()->getIsSupplier(),
                'isInOtherShop' => $product->getState()->getIsShop(), //
            ];
        }

        return new \Http\JsonResponse([
            'success'  => true,
            'products' => $productData,
        ]);
    }
}
