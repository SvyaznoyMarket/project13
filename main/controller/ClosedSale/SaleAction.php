<?php

namespace Controller\ClosedSale;


use \Exception\AccessDeniedException;
use \Http\Response;
use Model\ClosedSale\ClosedSaleEntity;
use \View\ClosedSale\SaleIndexPage;
use \View\ClosedSale\SaleShowPage;
use \Model\Product\Entity as Product;


class SaleAction
{

    /** @var \Scms\Client */
    private $scmsClient;

    public function __construct()
    {
        $this->scmsClient = \App::scmsClient();

        if (!\App::user()->getToken()) {
            $exception = new AccessDeniedException();
            $exception->setRedirectUrl(\App::request()->getRequestUri());
            throw $exception;
        }

    }

    /**
     * Страница всех акций
     *
     * @return Response
     */
    public function index()
    {
        $page = new SaleIndexPage();

        $sales = $this->getSales();

        $page->setParam('sales', $sales);
        return new Response($page->show());
    }

    /**
     * Листинг одной акции
     *
     * @param $uid
     * @param \Http\Request $request
     *
     * @return \Http\JsonResponse|Response
     * @throws \Exception
     */
    public function show($uid, \Http\Request $request)
    {
        $page = new SaleShowPage();
        $pageNum = (int)$request->get('page', 1);
        $limit = \App::config()->product['itemsPerPage'];
        $selectedCategoryId = $request->query->get('category');
        $categories = [];

        $sales = $this->getSales();

        $currentSales = array_map(
            function (array $data) {
                return new ClosedSaleEntity($data);
            },
            $this->scmsClient->query('api/promo-sale/get', ['uid' => [$uid]], [])
        );

        $currentSale = array_key_exists(0, $currentSales) ? $currentSales[0] : new ClosedSaleEntity([]);

        $products = $currentSale->products;

        // получаем все продукты
        \RepositoryManager::product()->prepareProductQueries($products, 'model media label brand category');
        $this->scmsClient->execute();

        // исключаем модельные ряды по названию товара, пока нет времени это сделать на стороне scms
        $productName = null;
        foreach ($products as $key => $product ) {
            if ($productName === $product->getName()) {
                unset($products[$key]);
            } else {
                $productName = $product->getName();
            }
        }

        // убираем товары, которые нельзя купить
        /** @var \Model\Product\Entity[] $products */
        $products = array_filter($products, function(Product $product) { return $product->getIsBuyable(); } );

        // если в запросе есть ID категории, то отфильтруем товары
        if ($selectedCategoryId) {
            $products = array_filter($products,
                function(Product $product) use ($selectedCategoryId) {
                    return $product->getRootCategory() && $product->getRootCategory()->getId() == $selectedCategoryId;
                }
            );
        }

        // немного аналитики и достаём категории
        $categoryUids = [];
        foreach ($products as $product) {
            $product->setLink(
                $product->getLink() . (strpos($product->getLink(), '?') === false ? '?' : '&')
                . http_build_query([
                    'sender' => ['name' => 'secret_sale'],
                    'secretsaleUid' => $uid
                ])
            );

            if ($product->getRootCategory() && !in_array($product->getRootCategory()->id, $categoryUids, true)) {
                $categoryUids[] = $product->getRootCategory()->id;
            }
        }

        if ($categoryUids) {
            $categories = \RepositoryManager::productCategory()->getCollectionById($categoryUids);
        }

        // сортировка
        $productSorting = new \Model\Product\Sorting();
        list($sortingName, $sortingDirection) = array_pad(explode('-', $request->get('sort')), 2, null);
        $productSorting->setActive($sortingName, $sortingDirection);
        $this->sort($products, $sortingName, (bool) ('asc' === $sortingDirection) );

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
        if ($request->isXmlHttpRequest() && 'true' === $request->get('ajax')) {

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

        $page->setParam('sales', $sales);
        $page->setParam('currentSale', $currentSale);
        $page->setParam('productPager', $productPager);
        $page->setParam('products', $products);
        $page->setParam('categories', $categories);
        $page->setParam('productView', \Model\Product\Category\Entity::PRODUCT_VIEW_COMPACT);
        $page->setParam('productSorting', $productSorting);
        return new Response($page->show());
    }

    /**
     * @return ClosedSaleEntity[]
     */
    private function getSales()
    {
        return array_map(
            function (array $data) {
                return new ClosedSaleEntity($data);
            },
            $this->scmsClient->query('api/promo-sale/get', [], [])
        );
    }

    /**
     * @param array             $products
     * @param string            $sortName
     * @param bool              $sortAscDirection
     * @return bool
     */
    public function sort(&$products, $sortName = 'default', $sortAscDirection = true) {
        if ( !is_array($products) || count($products) === 0 ) {
            return false;
        }

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

        usort( $products, array($this, $compareFunctionName) );

        if (false === $sortAscDirection) {
            $products = array_reverse($products);
        }

        return true;
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