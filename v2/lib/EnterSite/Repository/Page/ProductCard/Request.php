<?php

namespace EnterSite\Repository\Page\ProductCard;

use EnterSite\Model;
use EnterSite\Repository;

class Request extends Repository\Page\DefaultLayout\Request {
    /** @var Model\Product */
    public $product;
}