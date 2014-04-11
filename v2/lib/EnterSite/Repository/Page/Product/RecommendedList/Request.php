<?php

namespace EnterSite\Repository\Page\Product\RecommendedList;

use EnterSite\Model;

class Request {
    /** @var Model\Product */
    public $product;
    /** @var Model\Product[] */
    public $productsById;
    /** @var string[] */
    public $alsoBoughtIdList = [];
    /** @var string[] */
    public $similarIdList = [];
    /** @var string[] */
    public $alsoViewedIdList = [];
}