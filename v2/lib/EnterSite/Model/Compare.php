<?php

namespace EnterSite\Model;

use EnterSite\Model;

class Compare implements \Countable {
    /** @var Model\Compare\Product[] */
    public $product = [];

    /**
     * @return int
     */
    public function count() {
        return count($this->product);
    }
}