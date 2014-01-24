<?php

namespace EnterSite\Repository\Page\ProductCatalog\ChildCategory;

use EnterSite\Model;

class Request {
    /** @var Model\MainMenu[] */
    public $mainMenuList = [];
    /** @var Model\Region */
    public $region;
    /** @var Model\Product\Category */
    public $category;
    /** @var Model\Product\Catalog\Config */
    public $catalogConfig;
    /** @var Model\Product[] */
    public $products = [];
    /** @var Model\Product\RequestFilter[] */
    public $requestFilters = [];
    /** @var Model\Product\Sorting */
    public $sorting;
    /** @var int */
    public $pageNum;
}