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
    use EnterSite\Model\Partial;

    class Content extends Page\DefaultLayout\Content {
        /** @var Content\ProductBlock|null */
        public $productBlock;
        /** @var Content\FilterBlock|null */
        public $filterBlock;
        /** @var Partial\SelectedFilterBlock|null */
        public $selectedFilterBlock;
        /** @var Content\SortingBlock|null */
        public $sortingBlock;
        /** @var bool */
        public $hasCustomStyle;

        public function __construct() {
            parent::__construct();
        }
    }
}

namespace EnterSite\Model\Page\ProductCatalog\ChildCategory\Content {
    use EnterSite\Model\Partial;

    class ProductBlock {
        /** @var Partial\ProductCard[] */
        public $products = [];
        /** @var int */
        public $limit;
        /** @var string */
        public $url;
        /** @var string */
        public $dataValue;
        /** @var string */
        public $dataReset;
        /** @var Partial\Link|null */
        public $moreLink;
    }

    class FilterBlock {
        /** @var Partial\ProductFilter[] */
        public $filters = [];
    }

    class SortingBlock {
        /** @var Partial\Sorting[] */
        public $sortings = [];
    }
}
