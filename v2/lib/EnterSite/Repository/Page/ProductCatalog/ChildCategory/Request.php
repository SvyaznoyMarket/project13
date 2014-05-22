<?php

namespace EnterSite\Repository\Page\ProductCatalog\ChildCategory;

use EnterSite\Model;
use EnterSite\Repository;

class Request extends Repository\Page\DefaultLayout\Request {
    /** @var Model\Product\Category */
    public $category;
    /** @var Model\Product\Catalog\Config */
    public $catalogConfig;
    /** @var Model\Product\RequestFilter[] */
    public $requestFilters = [];
    /** @var Model\Product\Sorting */
    public $sorting;
    /** @var int */
    public $pageNum;
    /** @var int */
    public $limit;
    /** @var int */
    public $count;
    /** @var Model\Product[] */
    public $products = [];
}