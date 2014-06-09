<?php

namespace EnterSite\Model\Page\ProductCatalog {
    use EnterSite\Model\Page;

    class RootCategory extends Page\DefaultLayout {
        /** @var RootCategory\Content */
        public $content;

        public function __construct() {
            parent::__construct();

            $this->content = new RootCategory\Content();
        }
    }
}

namespace EnterSite\Model\Page\ProductCatalog\RootCategory {
    use EnterSite\Model\Page;
    use EnterSite\Model\Partial;

    class Content extends Page\DefaultLayout\Content {
        /** @var Partial\ProductCatalog\CategoryBlock|null */
        public $categoryBlock;

        public function __construct() {
            parent::__construct();
        }
    }
}

