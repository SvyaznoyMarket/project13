<?php

namespace EnterSite\Model\Partial {
    use EnterSite\Model\Partial;

    class ProductSlider {
        /** @var Partial\ProductCard[] */
        public $productCards = [];
        /** @var ProductSlider\Category[] */
        public $categories = [];
        /** @var bool */
        public $hasCategories;
    }
}

namespace EnterSite\Model\Partial\ProductSlider {
    class Category {
        /** @var string */
        public $name;
        /** @var string */
        public $id;
    }
}