<?php

namespace EnterTerminal\Model\Page\ProductCatalog {
    use EnterSite\Model;

    class ChildCategory {
        /** @var Model\Region */
        public $region;
        /** @var Model\Product\Category */
        public $category;
        /** @var Model\Product\Catalog\Config */
        public $catalogConfig;
        /** @var Model\Product[] */
        public $products = [];
        /** @var Model\Product\Sorting[] */
        public $sorting = [];
    }
}
