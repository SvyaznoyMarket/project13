<?php

namespace Controller\Product;

use Templating\HtmlLayout;

class SetAction {
    /**
     * @param string        $productBarcodes Например, '2070903000023,2070903000054,2070902000000'
     * @param \Http\Request $request
     * @return \Http\Response
     * @throws \Exception\NotFoundException
     */
    public function execute($productBarcodes, \Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $client = \App::coreClientV2();

        $productBarcodes = explode(',', $productBarcodes);
        if (!(bool)$productBarcodes) {
            throw new \Exception\NotFoundException('Не передано ни одного баркода товара');
        }

        // подготовка 1-го запроса

        /** @var $categoriesById \Model\Product\Category\Entity[] */
        $categoriesById = [];
        /** @var $products \Model\Product\ExpandedEntity */
        $products = [];
        /** @var $products \Model\Product\Entity */
        $productsForRetargeting = [];
        \RepositoryManager::product()->prepareCollectionByBarcode($productBarcodes, \App::user()->getRegion(), function($data) use (&$products, &$categoriesById, &$productsForRetargeting) {
            foreach ($data as $item) {
                $products[] = new \Model\Product\ExpandedEntity($item);
                $productsForRetargeting[] = new \Model\Product\Entity($item);

                if (isset($item['category']) && is_array($item['category'])) {
                    $categoryItem = array_pop($item['category']);
                    if (is_array($categoryItem)) {
                        $categoriesById[$categoryItem['id']] = new \Model\Product\Category\Entity($categoryItem);
                    }
                }
            }
        });

        // выполнение 1-го запроса
        $client->execute();

        $pager = new \Iterator\EntityPager($products, count($products));
        $pager->setPage(1);
        $pager->setMaxPerPage(100);


        $page = new \View\Product\SetPage();
        $page->setParam('pager', $pager);
        $page->setParam('products', $productsForRetargeting);
        $page->setParam('categoriesById', $categoriesById);

        return new \Http\Response($page->show());
    }

    /**
     * @param string        $productBarcodes
     * @param \Http\Request $request
     * @return \Http\JsonResponse
     * @throws \Exception\NotFoundException
     */
    public function widget($productBarcodes, \Http\Request $request) {
        \App::logger()->debug('Exec ' . __METHOD__);

        $client = \App::coreClientV2();

        $productBarcodes = explode(',', $productBarcodes);
        if (!(bool)$productBarcodes) {
            throw new \Exception\NotFoundException('Не передан ни один ид товара');
        }

        // подготовка 1-го запроса

        /** @var $products \Model\Product\ExpandedEntity */
        $products = [];
        \RepositoryManager::product()->prepareCollectionByBarcode($productBarcodes, \App::user()->getRegion(), function($data) use (&$products) {
            foreach ($data as $item) {
                $products[] = new \Model\Product\CompactEntity($item);
            }
        });

        // выполнение 1-го запроса
        $client->execute();

        $pager = new \Iterator\EntityPager($products, count($products));
        $pager->setPage(1);
        $pager->setMaxPerPage(100);


        return new \Http\JsonResponse([
            'success' => true,
            'content' => (new HtmlLayout())->render('product/_pager', ['pager' => $pager]),
        ]);
    }
}