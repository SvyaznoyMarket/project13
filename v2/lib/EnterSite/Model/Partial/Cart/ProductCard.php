<?php

namespace EnterSite\Model\Partial\Cart;

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
    /** @var Partial\Cart\ProductSum|null */
    public $sum;
    /** @var int */
    public $oldPrice;
    /** @var string */
    public $shownOldPrice;
    /** @var string */
    public $image;
    /** @var string */
    public $id;
    /** @var Partial\Cart\ProductSpinner|null */
    public $cartSpinner;
    /** @var Partial\Cart\ProductDeleteButton|null */
    public $cartDeleteButton;
}