<?php

namespace EnterSite\Repository\Page\ProductCard;

use EnterSite\Model;
use EnterSite\Repository;

class Request extends Repository\Page\DefaultLayout\Request {
    /** @var Model\Product */
    public $product;
    /** @var Model\Product\Category[] */
    public $accessoryCategories = [];
    /** @var Model\Product\Review[] */
    public $reviews = [];
}