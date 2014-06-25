<?php

namespace EnterSite\Model\Partial\ProductCatalog {
    use EnterSite\Model\Partial;

    class CategoryBlock {
        /** @var CategoryBlock\Category[] */
        public $categories = [];
    }
}

namespace EnterSite\Model\Partial\ProductCatalog\CategoryBlock {
    use EnterSite\Model\Partial;

    class Category {
        /** @var string */
        public $name;
        /** @var string */
        public $image;
        /** @var string */
        public $url;
    }
}