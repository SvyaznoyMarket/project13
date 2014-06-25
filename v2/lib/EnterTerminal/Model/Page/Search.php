<?php

namespace EnterTerminal\Model\Page {
    use EnterSite\Model;

    class Search {
        /** @var string */
        public $searchPhrase;
        /** @var Model\Product[] */
        public $products = [];
        /** @var int */
        public $productCount;
        /** @var Model\Product\Sorting[] */
        public $sortings = [];
        /** @var Model\Product\Filter[] */
        public $filters = [];
    }
}
