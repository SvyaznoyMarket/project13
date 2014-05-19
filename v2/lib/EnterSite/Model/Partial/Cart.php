<?php

namespace EnterSite\Model\Partial;

use EnterSite\Model\Partial;

class Cart extends Partial\Widget {
    public $widgetType = 'cart';
    /** @var float */
    public $sum;
    /** @var string */
    public $shownSum;
    /** @var int */
    public $quantity;
    /** @var string */
    public $shownQuantity;
    /** @var Partial\DirectCredit|null */
    public $credit;
}