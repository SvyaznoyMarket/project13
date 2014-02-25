<?php

namespace EnterSite\Model\Product;

use EnterSite\Model;

class Relation {
    /** @var Model\Product[] */
    public $accessories = [];
    /** @var Model\Product[] */
    public $similar = [];
}