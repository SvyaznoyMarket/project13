<?php

namespace EnterSite\Repository\Page\Product\ListByFilter;

use EnterSite\Model;

class Request {
    /** @var Model\Product\Filter[] */
    public $filters = [];
    /** @var Model\Product\RequestFilter[] */
    public $requestFilters = [];
    /** @var Model\Product\Sorting */
    public $sorting;
    /** @var Model\Product\Sorting[] */
    public $sortings = [];
    /** @var int */
    public $pageNum;
    /** @var int */
    public $limit;
    /** @var Model\Product[] */
    public $products = [];
    /** @var int */
    public $count;
}