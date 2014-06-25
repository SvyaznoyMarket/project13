<?php

namespace EnterSite\Model\Partial;

use EnterSite\Model\Partial;

class Rating {
    /** @var int */
    public $reviewCount;
    /** @var Partial\Rating\Star[] */
    public $stars = [];
}