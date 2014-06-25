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
    use EnterSite\Model\Partial;

    class Content extends Page\DefaultLayout\Content {
        /** @var Content\ProductBlock */
        public $productBlock;
        /** @var Partial\Cart */
        public $cart;
        /** @var string */
        public $orderUrl;

        public function __construct() {
            parent::__construct();

            $this->productBlock = new Content\ProductBlock();
            $this->cart = new Partial\Cart();
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