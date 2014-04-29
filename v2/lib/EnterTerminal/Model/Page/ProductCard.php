<?php

namespace EnterTerminal\Model\Page {
    use EnterSite\Model;

    class ProductCard {
        /** @var Model\Region */
        public $region;
        /** @var Model\Product */
        public $product;
        /** @var Model\Product\Review[] */
        public $reviews = [];
    }
}
