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
        $limit = \App::config()->product['itemsPerPage'];
        $pageNum = (int)$request->get('page', 1);
        $productBarcodes = explode(',', $productBarcodes);

        if (!(bool)$productBarcodes) {
            throw new \Exception\NotFoundException('Не передано ни одного баркода товара');
        }

        if ($pageNum < 1) {
            throw new \Exception\NotFoundException(sprintf('Неверный номер страницы "%s"', $pageNum));
        }

        $productVideosByProduct = []; // todo
        $productView = \Model\Product\Category\Entity::PRODUCT_VIEW_COMPACT; // вид товаров
        $client = \App::coreClientV2();


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

        $countProducts = count($products);
        if ($countProducts < $limit) {
            $limit = $countProducts;
        }


        $productPager = new \Iterator\EntityPager($products, $countProducts);
        $productPager->setPage($pageNum);
        $productPager->setMaxPerPage($limit);


        // проверка на максимально допустимый номер страницы
        if (($productPager->getPage() - $productPager->getLastPage()) > 0) {
            throw new \Exception\NotFoundException(sprintf('Неверный номер страницы "%s".', $productPager->getPage()));
            /*return new \Http\JsonResponse([
                'list' => [
                    'products' => [],
                    'productCount' => 0,
                ]
            ]);*/
        }


        // сортировка
        $productSorting = new \Model\Product\Sorting();
        list($sortingName, $sortingDirection) = array_pad(explode('-', $request->get('sort')), 2, null);
        $productSorting->setActive($sortingName, $sortingDirection);


        // ajax
        if ($request->isXmlHttpRequest() && 'true' == $request->get('ajax')) {

            $templating = \App::closureTemplating();
            $helper = $templating->getParam('helper');
            /** @var $helper \Helper\TemplateHelper */

            return new \Http\JsonResponse([
                'list'           => (new \View\Product\ListAction())->execute(
                        $helper,
                        $productPager,
                        $productVideosByProduct,
                        !empty($catalogJson['bannerPlaceholder']) ? $catalogJson['bannerPlaceholder'] : []
                    ),
                //'selectedFilter' => $selectedFilter,
                'pagination'     => (new \View\PaginationAction())->execute(
                        $helper,
                        $productPager
                    ),
                'sorting'        => (new \View\Product\SortingAction())->execute(
                        $templating->getParam('helper'),
                        $productSorting
                    ),
                /*'page'           => [
                    //'title'      => 'Тег «'.$tag->getName() . '»' . ( $selectedCategory ? ( ' — ' . $selectedCategory->getName() ) : '' )
                ],*/
            ]);
        }

        $page = new \View\Product\SetPage();
        $page->setParam('productPager', $productPager);
        $page->setParam('products', $productsForRetargeting);
        $page->setParam('categoriesById', $categoriesById);
        $page->setParam('productView', $productView);
        $page->setParam('productSorting', $productSorting);
        $page->setParam('productVideosByProduct', $productVideosByProduct);

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