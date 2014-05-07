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
    /** @var int */
    public $oldPrice;
    /** @var string */
    public $shownOldPrice;
    /** @var string */
    public $image;
    /** @var string */
    public $id;
    /** @var string */
    public $categoryId;
    /** @var Partial\Cart\ProductButton|null */
    public $cartButton;
    /** @var Partial\Rating|null */
    public $rating;
}