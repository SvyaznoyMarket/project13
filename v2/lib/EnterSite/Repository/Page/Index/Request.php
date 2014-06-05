<?php

namespace EnterSite\Repository\Page\Index;

use Enter\Http;
use EnterSite\Model;
use EnterSite\Repository;

class Request extends Repository\Page\DefaultLayout\Request {
    /** @var Model\Product\Category[] */
    public $categories = [];
    /** @var Model\Promo[] */
    public $promos = [];
}