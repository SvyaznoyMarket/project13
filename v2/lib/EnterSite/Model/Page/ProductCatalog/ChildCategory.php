<?php

namespace EnterSite\Model\Page\ProductCatalog {
    use EnterSite\Model\Page;

    class ChildCategory extends Page\DefaultLayout {
        /** @var ChildCategory\Content */
        public $content;

        public function __construct() {
            parent::__construct();

            $this->content = new ChildCategory\Content();
        }
    }
}

namespace EnterSite\Model\Page\ProductCatalog\ChildCategory {
    use EnterSite\Model\Page;

    class Content extends Page\DefaultLayout\Content {
        /** @var Content\ProductBlock */
        public $productBlock;
        /** @var bool */
        public $hasCustomStyle;

        public function __construct() {
            parent::__construct();

            $this->productBlock = new Content\ProductBlock();
        }
    }
}

namespace EnterSite\Model\Page\ProductCatalog\ChildCategory\Content {
    class ProductBlock {
        /** @var ProductBlock\ProductCard[] */
        public $products = [];
    }
}

namespace EnterSite\Model\Page\ProductCatalog\ChildCategory\Content\ProductBlock {
    class ProductCard {
        /** @var string */
        public $name;
        /** @var string */
        public $url;
    }
}