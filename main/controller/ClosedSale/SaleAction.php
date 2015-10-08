<?php

namespace Controller\ClosedSale;


use \Exception\AccessDeniedException;
use Http\RedirectResponse;
use \Http\Response;
use Model\ClosedSale\ClosedSaleEntity;
use \View\ClosedSale\SaleIndexPage;
use \View\ClosedSale\SaleShowPage;


class SaleAction
{

    /** @var \Scms\Client */
    private $scmsClient;

    public function __construct()
    {
        $this->scmsClient = \App::scmsClient();
    }

    public function index()
    {
        $page = new SaleIndexPage();

        $sales = $this->getSales();

        $page->setParam('sales', $sales);
        return new Response($page->show());
    }

    public function show($uid)
    {
        $page = new SaleShowPage();

        $sales = $this->getSales();

        $currentSales = array_map(
            function (array $data) {
                return new ClosedSaleEntity($data);
            },
//            $this->scmsClient->query('api/promo-sale/get', ['uid' => [$uid]], [])
            \App::dataStoreClient()->query('/fixture/promo-sale-get-one.json')
        );

        $currentSale = array_key_exists(0, $currentSales) ? $currentSales[0] : new ClosedSaleEntity([]);

        $products = $currentSale->products;

        \RepositoryManager::product()->prepareProductQueries($products, ['media', 'label', 'brand', 'category']);
        \App::curl()->execute();

        $page->setParam('sales', $sales);
        $page->setParam('currentSale', $currentSale);
        $page->setParam('products', $products);
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
//            $this->scmsClient->query('api/promo-sale/get', [], [])
        \App::dataStoreClient()->query('/fixture/promo-sale-get.json')
        );
    }

}