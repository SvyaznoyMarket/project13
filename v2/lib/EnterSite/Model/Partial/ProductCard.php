<?php

namespace EnterSite\Model\Partial;

use EnterSite\Model\Partial;

class ProductCard {
    /** @var string */
    public $name;
    /** @var string */
    public $url;
    /** @var int */
    public $price;
    /** @var string */
    public $shownPrice;
    /** @var string */
    public $image;
    /** @var Partial\Cart\ProductButton|null */
    public $cartButton;
}