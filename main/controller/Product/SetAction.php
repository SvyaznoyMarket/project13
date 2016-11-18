<?php

namespace Controller\Product;

use Templating\HtmlLayout;

class SetAction {
    /**
     * @param string        $productBarcodes Например, '2070903000023,2070903000054,2070902000000'
     * @param \Http\Request $request
     * @param string|null $setTitle Название сета
     * @param string|null $page
     * @return \Http\Response
     * @throws \Exception\NotFoundException
     */
    public function execute($productBarcodes, \Http\Request $request, $setTitle = null, $page = null) {
        if (!isset($page) && $request->query->get('page')) {
            return new \Http\RedirectResponse((new \Helper\TemplateHelper())->replacedUrl([
                'page' => (int)$request->query->get('page'),
            ]), 301);
        }

        if (isset($page) && $page <= 1) {
            return new \Http\RedirectResponse((new \Helper\TemplateHelper())->replacedUrl([], ['page'], $request->routeName), 301);
        }

        // Например, ести url = .../page-02
        if (isset($page) && (string)(int)$page !== $page) {
            return new \Http\RedirectResponse((new \Helper\TemplateHelper())->replacedUrl([
                'page' => (int)$page,
            ]), 301);
        }

        $page = (int)$page ?: 1;

        $limit = \App::config()->product['itemsPerPage'];
        $productBarcodes = explode(',', $productBarcodes);

        if (!$productBarcodes) {
            throw new \Exception\NotFoundException('Не передано ни одного баркода товара');
        }

        /** @var \Model\Product\Entity[] $products */
        $products = array_map(function($productBarcode) { return new \Model\Product\Entity(['bar_code' => $productBarcode]); }, $productBarcodes);

        \RepositoryManager::product()->prepareProductQueries($products, 'model media label brand category');
        \App::coreClientV2()->execute();

        if (\App::config()->product['reviewEnabled']) {
            \RepositoryManager::review()->prepareScoreCollection($products, function($data) use(&$products) {
                if (isset($data['product_scores'][0])) {
                    \RepositoryManager::review()->addScores($products, $data);
                }
            });
            \App::coreClientV2()->execute();
        }

        //$products = array_filter($products, function(\Model\Product\Entity $product) { return $product->isAvailable(); });

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
                $limit * ($page - 1),
                $limit
            );
        };

        if (0 < $productCount && $productCount < $limit) {
            $limit = $productCount;
        }

        // productPager Entity
        $productPager = new \Iterator\EntityPager($products, $productCount);
        $productPager->setPage($page);
        $productPager->setMaxPerPage($limit);

        if (($productPager->getPage() - $productPager->getLastPage()) > 0) {
            return new \Http\RedirectResponse((new \Helper\TemplateHelper())->replacedUrl([
                'page' => $productPager->getLastPage(),
            ]));
        }

        $helper = new \Helper\TemplateHelper();

        $listViewData = (new \View\Product\ListAction())->execute(
            $helper,
            $productPager
        );

        if ($request->isXmlHttpRequest() && 'true' == $request->get('ajax')) {
            return new \Http\JsonResponse([
                'list'           => $listViewData,
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
                'request' => [
                    'route' => [
                        'name' => \App::request()->routeName,
                        'pathVars' => \App::request()->routePathVars->all(),
                    ],
                ],
            ]);
        }

        // страница
        $pageView = new \View\Product\SetPage();
        $pageView->setParam('productPager', $productPager);
        $pageView->setParam('products', $products);
        $pageView->setParam('categoriesById', []);
        $pageView->setParam('productSorting', $productSorting);
        $pageView->setParam('pageTitle', (string)$setTitle);
        $pageView->setParam('listViewData', $listViewData);

        return new \Http\Response($pageView->show());
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
        $a = $productX->getAvgScore();
        $b = $productY->getAvgScore();

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