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

        $page = new \Terminal\View\ProductCategory\IndexPage();
        $page->setParam('category', $category);

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

        $client = \App::coreClientV2();
        $user = \App::user();
        $sorting = $request->get('sort', ['type' => 'score', 'direction' => 'desc']);
        $filter = $request->get('filter', []);
        $offset =  (int)$request->get('offset', 0);
        $limit = (int)$request->get('limit', 32);

        // запрашиваем категорию по id
        $category = \RepositoryManager::productCategory()->getEntityById($categoryId);

        if (!$category) {
            throw new \Exception\NotFoundException(sprintf('Категория товара #%s не найдена.', $categoryId));
        }

        $productFilter = new \Model\Product\Filter($filter, false);
        $productFilter->setCategory($category);

        $productSorting = new \Model\Product\Sorting();
        $productSorting->setActive($sorting['type'], $sorting['direction']);

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
                'id'           => $product->getId(),
                'name'         => $product->getName(),
                'image'        => $product->getImageUrl(3),
                'article'      => $product->getArticle(),
                'price'        => $product->getPrice(),
                'description'  => $product->getDescription(),
                'isBuyable'    => $product->getIsBuyable($shopId),
                'isInShop'     => $product->getIsInShop($shopId),
                'isInShowroom' => $product->getIsInShowroom($shopId),
            ];
        }

        return new \Http\JsonResponse([
            'success'  => true,
            'products' => $productData,
        ]);
    }
}
