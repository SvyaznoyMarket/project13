<?php

namespace EnterSite\Model\Page {
    use EnterSite\Model\Page;

    class ProductCard extends Page\DefaultLayout {
        /** @var ProductCard\Content */
        public $content;

        public function __construct() {
            parent::__construct();

            $this->content = new ProductCard\Content();
        }
    }
}

namespace EnterSite\Model\Page\ProductCard {
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

namespace EnterSite\Model\Page\ProductCard\Content {
    use EnterSite\Model\Partial;

    class ProductBlock {
        /** @var string */
        public $title;
    }
}
