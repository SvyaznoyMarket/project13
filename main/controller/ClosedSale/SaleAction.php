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

        $sales = array_map(
            function (array $data) {
                return new ClosedSaleEntity($data);
            },
            $this->scmsClient->query('api/promo-sale/get', [], [])
        );

        $page->setParam('sales', $sales);
        return new Response($page->show());
    }

    public function show($uid)
    {
        $page = new SaleShowPage();
        return new Response($page->show());
    }

}