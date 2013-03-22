<?php

namespace Terminal\Controller\ProductCategory;

class IndexAction {
    public function execute($categoryId, \Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $client = \App::coreClientV2();
        $user = \App::user();
        $sorting = $request->get('sort', ['type' => 'score', 'direction' => 'desc']);
        $filter = $request->get('filter', []);
        $is_json = $request->get('is_json', false);
        $offset =  (int)$request->get('offset', 0);
        $limit = (int)$request->get('limit', 32);

        $data = [
            'client_id' => \App::config()->coreV2['client_id'],
            'shop_id'   => \App::config()->region['shop_id'],
            'category'  => $categoryId,
            'sorting'   => $sorting,
            'filter'    => $filter,
        ];

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

        $response = null;
        if ($is_json) {
            $response = new \Http\JsonResponse(array_map(function ($item) {
                return [
                    'id' => $item->getId(),
                    'name' => $item->getName(),
                    'image' => $item->getImageUrl(3),
                    'article' => $item->getArticle(),
                    'price' => $item->getPrice(),
                    'isBuyable' => $item->getIsBuyable(\App::config()->region['shop_id']),
                ];
            }, $collection));
        } else {
            $page = new \Terminal\View\ProductCategory\IndexPage();
            $page->setParam('data', $data);
            $page->setParam('products', $collection);

            $response = new \Http\Response($page->show());
        }

        return $response;
    }
}
