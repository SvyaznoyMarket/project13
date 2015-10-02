<?php

namespace Controller\Wishlist;

use EnterApplication\CurlTrait;
use EnterApplication\Action\ActionTrait;
use EnterQuery as Query;

class ShowAction {
    use CurlTrait;
    use ActionTrait;

    /**
     * @param $wishlistToken
     * @param \Http\Request $request
     * @return \Http\Response
     * @throws \Exception
     */
    public function execute($wishlistToken, \Http\Request $request) {
        //\App::logger()->debug('Exec ' . __METHOD__);

        $wishlistToken = $request->get('wishlistToken');
        $pageNum = (int)$request->get('page', 1);
        $limit = \App::config()->product['itemsPerPage'];

        if (empty($wishlistToken)) {
            throw new \Exception\NotFoundException('Не передан wishlistToken');
        }
        if ($pageNum < 1) {
            throw new \Exception\NotFoundException(sprintf('Неверный номер страницы %s', $pageNum));
        }

        $regionQuery = $this->getRegionQuery(\App::user()->getRegionId());
        $regionQuery->prepare();

        $wishlistQuery = new Query\User\Wishlist\GetByToken();
        $wishlistQuery->token = $wishlistToken;
        $wishlistQuery->filter->withProducts = true;
        $wishlistQuery->prepare();

        $this->getCurl()->execute();

        if ($error = $wishlistQuery->error) {
            throw $error;
        }

        // проверка региона
        $this->checkRegionQuery($regionQuery);

        $productUis =
            isset($wishlistQuery->response->wishlist['products'][0])
            ? array_column($wishlistQuery->response->wishlist['products'], 'uid')
            : []
        ;

        /** @var \Model\Product\Entity[] $productsByUi */
        $productsByUi = [];
        foreach ($productUis as $productUi) {
            $productsByUi[$productUi] = new \Model\Product\Entity(['ui' => $productUi]);
        }

        \RepositoryManager::product()->prepareProductQueries($productsByUi, 'media label brand category');
        \App::coreClientV2()->execute();

        //$products = array_filter($products, function(\Model\Product\Entity $product) { return $product->isAvailable(); });

        // сортировка
        $productSorting = new \Model\Product\Sorting();
        list($sortingName, $sortingDirection) = array_pad(explode('-', $request->get('sort')), 2, null);
        $productSorting->setActive($sortingName, $sortingDirection);
        $this->sort($productsByUi, $sortingName, (bool) ('asc' == $sortingDirection) );

        // пагинация
        $productCount = count($productsByUi);

        if ($productCount > $limit) {
            $productsByUi = array_slice(
                $productsByUi,
                $limit * ($pageNum - 1),
                $limit
            );
        };

        if (0 < $productCount && $productCount < $limit) {
            $limit = $productCount;
        }

        // productPager Entity
        $productPager = new \Iterator\EntityPager($productsByUi, $productCount);
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
        $page->setParam('products', $productsByUi);
        $page->setParam('categoriesById', []);
        $page->setParam('productView', \Model\Product\Category\Entity::PRODUCT_VIEW_COMPACT);
        $page->setParam('productSorting', $productSorting);
        $page->setParam('pageTitle', (string)$setTitle);

        return new \Http\Response($page->show());
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