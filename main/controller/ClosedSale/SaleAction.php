<?php

namespace Controller\ClosedSale;


use \Exception\AccessDeniedException;
use \Http\Response;
use \View\ClosedSale\SaleIndexPage;
use \View\ClosedSale\SaleShowPage;


class SaleAction
{

    public function __construct()
    {
        if (!\App::user()->getEntity()) {
            // throw new AccessDeniedException();
        }
    }

    public function index()
    {
        $page = new SaleIndexPage();
        return new Response($page->show());
    }

    public function show($uid)
    {
        $page = new SaleShowPage();
        return new Response($page->show());
    }

}