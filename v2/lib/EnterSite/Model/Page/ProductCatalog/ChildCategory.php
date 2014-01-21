<?php

namespace EnterSite\Model\Page\ProductCatalog {
    use EnterSite\Model;

    class ChildCategory extends Model\Page\DefaultLayout {
        /** @var ChildCategory\Content */
        public $content;

        public function __construct() {
            $this->content = new ChildCategory\Content();
        }
    }
}

namespace EnterSite\Model\Page\ProductCatalog\ChildCategory {
    use EnterSite\Model;

    class Content {
        /** @var string */
        public $title;
        /** @var Content\ProductBlock */
        public $productBlock;
        /** @var bool */
        public $hasCustomStyle;
        /** @var Model\Product\Catalog\Config\PromoStyle */
        public $promoStyle;

        public function __construct() {
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