<?php

namespace EnterTerminal\Model\Page {
    use EnterSite\Model;

    class ProductCard {
        /** @var Model\Product */
        public $product;
        /** @var Model\Product\Review[] */
        public $reviews = [];
    }
}
