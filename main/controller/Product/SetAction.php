<?php

namespace Controller\Product;

use Templating\HtmlLayout;

class SetAction {
    /**
     * @param string        $productBarcodes Например, '2070903000023,2070903000054,2070902000000'
     * @param \Http\Request $request
     * @param string|null $setTitle Название сета
     * @return \Http\Response
     * @throws \Exception\NotFoundException
     */
    public function execute($productBarcodes, \Http\Request $request, $setTitle = null) {
        //\App::logger()->debug('Exec ' . __METHOD__);
        $limit = \App::config()->product['itemsPerPage'];
        $pageNum = (int)$request->get('page', 1);
        $productBarcodes = explode(',', $productBarcodes);

        if (!(bool)$productBarcodes) {
            throw new \Exception\NotFoundException('Не передано ни одного баркода товара');
        }

        if ($pageNum < 1) {
            throw new \Exception\NotFoundException(sprintf('Неверный номер страницы "%s"', $pageNum));
        }

        $productView = \Model\Product\Category\Entity::PRODUCT_VIEW_COMPACT; // вид товаров
        $client = \App::coreClientV2();


        // подготовка 1-го запроса

        /** @var $categoriesById \Model\Product\Category\Entity[] */
        $categoriesById = [];
        /** @var $products \Model\Product\Entity[] */
        $products = [];

        \RepositoryManager::product()->prepareCollectionByBarcode($productBarcodes, \App::user()->getRegion(), function($data) use (&$products, &$categoriesById) {
            foreach ($data as $item) {
                //$products[] = new \Model\Product\ExpandedEntity($item);
                $product = new \Model\Product\Entity($item);
                if (!$product->isAvailable()) continue;

                $products[] = $product;
                //$productsForRetargeting[] = new \Model\Product\Entity($item);

                /* // SITE-2886 — В подборках не выводить список категорий товаров
                if (isset($item['category']) && is_array($item['category'])) {
                    $categoryItem = array_pop($item['category']);
                    if (is_array($categoryItem)) {
                        $categoriesById[$categoryItem['id']] = new \Model\Product\Category\Entity($categoryItem);
                    }
                }*/
            }
        });

        // выполнение 1-го запроса
        $client->execute();

        \RepositoryManager::product()->prepareProductsMedias($products);
        $client->execute();

        // сортировка
        $productSorting = new \Model\Product\Sorting();
        list($sortingName, $sortingDirection) = array_pad(explode('-', $request->get('sort')), 2, null);
        $productSorting->setActive($sortingName, $sortingDirection);
        $this->sort($products, $sortingName, (bool) ('asc' == $sortingDirection) );

        // пагинация
        $productCount = count($products);

        if ($productCount > $limit) {
            $products = array_slice(
                $products,
                $limit * ($pageNum - 1),
                $limit
            );
        };

        if (0 < $productCount && $productCount < $limit) {
            $limit = $productCount;
        }

        // productPager Entity
        $productPager = new \Iterator\EntityPager($products, $productCount);
        $productPager->setPage($pageNum);
        $productPager->setMaxPerPage($limit);

        // проверка на максимально допустимый номер страницы
        if (($productPager->getPage() - $productPager->getLastPage()) > 0) {
            throw new \Exception\NotFoundException(sprintf('Неверный номер страницы "%s".', $productPager->getPage()));
        }

        // ajax
        if ($request->isXmlHttpRequest() && 'true' == $request->get('ajax')) {

            $templating = \App::closureTemplating();
            $helper = $templating->getParam('helper');
            /** @var $helper \Helper\TemplateHelper */

            return new \Http\JsonResponse([
                'list'           => (new \View\Product\ListAction())->execute(
                    $helper,
                    $productPager,
                    !empty($catalogJson['bannerPlaceholder']) ? $catalogJson['bannerPlaceholder'] : []
                ),
                //'selectedFilter' => $selectedFilter,
                'pagination'     => (new \View\PaginationAction())->execute(
                    $helper,
                    $productPager
                ),
                'sorting'        => (new \View\Product\SortingAction())->execute(
                    $helper,
                    $productSorting
                ),
                /*'page'           => [
                    //'title'      => 'Тег «'.$tag->getName() . '»' . ( $selectedCategory ? ( ' — ' . $selectedCategory->getName() ) : '' )
                ],*/
            ]);
        }

        // страница
        $page = new \View\Product\SetPage();
        $page->setParam('productPager', $productPager);
        $page->setParam('products', $products);
        $page->setParam('categoriesById', $categoriesById);
        $page->setParam('productView', $productView);
        $page->setParam('productSorting', $productSorting);
        $page->setParam('pageTitle', (string)$setTitle);

        return new \Http\Response($page->show());
    }

    /**
     * @param string        $productBarcodes
     * @param \Http\Request $request
     * @return \Http\JsonResponse
     * @throws \Exception\NotFoundException
     */
    public function widget($productBarcodes, \Http\Request $request) {
        //\App::logger()->debug('Exec ' . __METHOD__);

        $client = \App::coreClientV2();

        $productBarcodes = explode(',', $productBarcodes);
        if (!(bool)$productBarcodes) {
            throw new \Exception\NotFoundException('Не передан ни один ид товара');
        }

        // подготовка 1-го запроса

        /** @var $products \Model\Product\Entity */
        $products = [];
        \RepositoryManager::product()->prepareCollectionByBarcode($productBarcodes, \App::user()->getRegion(), function($data) use (&$products) {
            foreach ($data as $item) {
                $products[] = new \Model\Product\Entity($item);
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


    /**
     * @param array             $products
     * @param string            $sortName
     * @param bool              $sortAscDirection
     * @return bool
     */
    public function sort(&$products, $sortName = 'default', $sortAscDirection = true) {
        if ( !is_array($products) || empty($products) ) return false;

        switch ($sortName) {
            case 'hits':
                $compareFunctionName = 'compareHits';
                break;
            case 'price':
                $compareFunctionName = 'comparePrice';
                break;
            default:
                $compareFunctionName = 'compareDefault';
        }

        //usort( $products, array(__CLASS__, $compareFunctionName) );
        usort( $products, array($this, $compareFunctionName) );

        if (false === $sortAscDirection) $products = array_reverse($products);
    }


    /**
     * Lambda function for compare by price
     *
     * @param \Model\Product\Entity $productX
     * @param \Model\Product\Entity $productY
     * @return int
     */
    private static function comparePrice(\Model\Product\Entity $productX, \Model\Product\Entity $productY/*, $depth = 0*/) {
        $a = $productX->getPrice();
        $b = $productY->getPrice();

        if ($a == $b) {
            return 0;
        }

        return ($a < $b) ? -1 : +1;
    }


    /**
     * Lambda function for default compare
     *
     * @param \Model\Product\Entity $productX
     * @param \Model\Product\Entity $productY
     * @return int
     */
    private static function compareDefault(\Model\Product\Entity $productX, \Model\Product\Entity $productY, $depth = 0) {
        $a = $productX->getIsBuyable();
        $b = $productY->getIsBuyable();
        //$sortAscDirection = true;

        if ($a == $b) {
            if (0 == $depth) {
                return self::compareHits($productX, $productY, ++$depth);
            } else {
                return 0;
            }
        }

        $ret = ( (int)$a < (int)$b ) ? -1 : +1;
        //if (!$sortAscDirection) $ret = !$ret;

        return $ret;
    }


    /**
     * Lambda function for hits (rating) compare
     *
     * @param \Model\Product\Entity $productX
     * @param \Model\Product\Entity $productY
     * @return int
     */
    private static function compareHits(\Model\Product\Entity $productX, \Model\Product\Entity $productY, $depth = 0) {
        $a = $productX->getRating();
        $b = $productY->getRating();

        if ($a == $b) {
            if (0 == $depth) {
                return self::compareDefault($productX, $productY, ++$depth);
            } else {
                return 0;
            }
        }

        return ( (int)$a < (int)$b ) ? -1 : +1;
    }


}