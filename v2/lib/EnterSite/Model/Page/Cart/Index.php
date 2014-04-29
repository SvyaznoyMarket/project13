<?php

namespace EnterSite\Model\Page\Cart {
    use EnterSite\Model\Page;

    class Index extends Page\DefaultLayout {
        /** @var Index\Content */
        public $content;

        public function __construct() {
            parent::__construct();

            $this->content = new Index\Content();
        }
    }
}

namespace EnterSite\Model\Page\Cart\Index {
    use EnterSite\Model\Page;

    class Content extends Page\DefaultLayout\Content {
        /** @var Content\ProductBlock */
        public $productBlock;

        public function __construct() {
            parent::__construct();

            $this->productBlock = new Content\ProductBlock();
        }
    }
}

namespace EnterSite\Model\Page\Cart\Index\Content {
    use EnterSite\Model\Partial;

    class ProductBlock {
        /** @var Partial\Cart\ProductCard[] */
        public $products = [];
    }
}
