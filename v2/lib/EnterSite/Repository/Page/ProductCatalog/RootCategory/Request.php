<?php

namespace EnterSite\Repository\Page\ProductCatalog\RootCategory;

use Enter\Http;
use EnterSite\Model;
use EnterSite\Repository;

class Request extends Repository\Page\DefaultLayout\Request {
    /** @var Model\Product\Category */
    public $category;
    /** @var Model\Product\Catalog\Config */
    public $catalogConfig;
}