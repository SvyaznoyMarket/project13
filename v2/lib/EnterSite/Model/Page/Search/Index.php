<?php

namespace EnterSite\Model\Page\Search {
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

namespace EnterSite\Model\Page\Search\Index {
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

