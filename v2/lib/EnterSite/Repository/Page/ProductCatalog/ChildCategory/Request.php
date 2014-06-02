<?php

namespace EnterSite\Repository\Page\ProductCatalog\ChildCategory;

use Enter\Http;
use EnterSite\Model;
use EnterSite\Repository;

class Request extends Repository\Page\DefaultLayout\Request {
    /** @var Model\Product\Category */
    public $category;
    /** @var Model\Product\Catalog\Config */
    public $catalogConfig;
    /** @var Model\Product\RequestFilter[] */
    public $requestFilters = [];
    /** @var Model\Product\Filter[] */
    public $filters = [];
    /** @var Model\Product\Sorting */
    public $sorting;
    /** @var Model\Product\Sorting[] */
    public $sortings = [];
    /** @var int */
    public $pageNum;
    /** @var int */
    public $limit;
    /** @var int */
    public $count;
    /** @var Model\Product[] */
    public $products = [];
    /** @var Http\Request */
    public $httpRequest;
}