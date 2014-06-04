<?php

namespace EnterTerminal\Model\Page\ProductCatalog {
    use EnterSite\Model;

    class Category {
        /** @var Model\Product\Category */
        public $category;
        /** @var Model\Product\Catalog\Config */
        public $catalogConfig;
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
