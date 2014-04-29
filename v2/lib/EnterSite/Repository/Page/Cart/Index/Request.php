<?php

namespace EnterSite\Repository\Page\Cart\Index;

use EnterSite\Model;
use EnterSite\Repository;

class Request extends Repository\Page\DefaultLayout\Request {
    /** @var Model\Product[] */
    public $productsById = [];
    /** @var Model\Cart\Product[] */
    public $cartProducts = [];
}