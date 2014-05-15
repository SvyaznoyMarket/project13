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
        /** @var Content\Cart */
        public $cart;

        public function __construct() {
            parent::__construct();

            $this->productBlock = new Content\ProductBlock();
            $this->cart = new Content\Cart();
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

namespace EnterSite\Model\Page\Cart\Index\Content {

    class Cart {
        /** @var float */
        public $sum;
        /** @var string */
        public $shownSum;
        /** @var int */
        public $quantity;
        /** @var string */
        public $shownQuantity;
    }
}
