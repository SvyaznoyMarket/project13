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
    public function show($uid, \Http\Request $request, $page = null)
    {
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

        $pageView = new SaleShowPage();
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

        if (\App::config()->product['reviewEnabled']) {
            \RepositoryManager::review()->prepareScoreCollection($products, function($data) use(&$products) {
                if (isset($data['product_scores'][0])) {
                    \RepositoryManager::review()->addScores($products, $data);
                }
            });
            \App::coreClientV2()->execute();
        }

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

        // проверка на максимально допустимый номер страницы
        if (($productPager->getPage() - $productPager->getLastPage()) > 0) {
            throw new \Exception\NotFoundException(sprintf('Неверный номер страницы "%s".', $productPager->getPage()));
        }

        $helper = new \Helper\TemplateHelper();

        $listViewData = (new \View\Product\ListAction())->execute(
            $helper,
            $productPager,
            [],
            null,
            true,
            4,
            \Model\Product\Category\Entity::VIEW_COMPACT,
            ['name' => 'secret_sale', 'position' => 'listing']
        );

        // ajax
        if ($request->isXmlHttpRequest() && 'true' === $request->get('ajax')) {
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

        $pageView->setParam('sales', $sales);
        $pageView->setParam('currentSale', $currentSale);
        $pageView->setParam('productPager', $productPager);
        $pageView->setParam('products', $products);
        $pageView->setParam('categories', $categories);
        $pageView->setParam('productSorting', $productSorting);
        $pageView->setParam('listViewData', $listViewData);
        return new Response($pageView->show());
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