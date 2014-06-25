<?php

namespace EnterTerminal\Model\Page {
    use EnterSite\Model;

    class Cart {
        /** @var float */
        public $sum;
        /** @var Model\Product[] */
        public $products = [];
    }
}
