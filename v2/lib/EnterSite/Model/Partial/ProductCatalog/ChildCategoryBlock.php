<?php

namespace EnterSite\Model\Partial\ProductCatalog {
    use EnterSite\Model\Partial;

    class ChildCategoryBlock {
        /** @var ChildCategoryBlock\Category[] */
        public $categories = [];
    }
}

namespace EnterSite\Model\Partial\ProductCatalog\ChildCategoryBlock {
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