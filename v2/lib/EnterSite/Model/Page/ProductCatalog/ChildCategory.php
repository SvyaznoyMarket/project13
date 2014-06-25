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
        /** @var Partial\ProductCatalog\CategoryBlock|null */
        public $categoryBlock;
        /** @var Partial\ProductBlock|null */
        public $productBlock;
        /** @var Partial\ProductFilterBlock|null */
        public $filterBlock;
        /** @var Partial\SelectedFilterBlock|null */
        public $selectedFilterBlock;
        /** @var Partial\SortingBlock|null */
        public $sortingBlock;

        public function __construct() {
            parent::__construct();
        }
    }
}

