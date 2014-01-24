<?php

namespace EnterSite\Model\Partial;

use EnterSite\Model\Partial;

class ProductCard {
    /** @var string */
    public $name;
    /** @var string */
    public $url;
    /** @var string */
    public $price;
    /** @var Partial\Cart\ProductButton|null */
    public $cartButton;
}