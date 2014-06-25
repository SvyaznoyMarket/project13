<?php

namespace EnterSite\Model\Page {
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

namespace EnterSite\Model\Page\Index {
    use EnterSite\Model\Page;
    use EnterSite\Model\Partial;

    class Content extends Page\DefaultLayout\Content {
        /** @var string */
        public $promoDataValue;

        public function __construct() {
            parent::__construct();
        }
    }
}

