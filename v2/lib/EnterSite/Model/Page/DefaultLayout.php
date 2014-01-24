<?php

namespace EnterSite\Model\Page {
    use EnterSite\Model\Page;

    class DefaultLayout extends Page {
        /** @var DefaultLayout\Header */
        public $header;
        /** @var DefaultLayout\MainMenu */
        public $mainMenu;
        /** @var DefaultLayout\Search */
        public $search;
        /** @var DefaultLayout\Content */
        public $content;

        public function __construct() {
            parent::__construct();

            $this->header = new DefaultLayout\Header();
            $this->mainMenu = new DefaultLayout\MainMenu();
            $this->search = new DefaultLayout\Search();
            $this->content = new DefaultLayout\Content();
        }
    }
}

namespace EnterSite\Model\Page\DefaultLayout {
    class Header {
        /** @var Header\RegionLink */
        public $regionLink;

        public function __construct() {
            $this->regionLink = new Header\RegionLink();
        }
    }

    class MainMenu {
    }

    class Search {
        /** @var string */
        public $inputPlaceholder;
        /** @var Search\Hint[] */
        public $hints = [];
    }

    class Content {
        /** @var string */
        public $title;

        public function __construct() {}
    }
}

namespace EnterSite\Model\Page\DefaultLayout\Header {
    class RegionLink {
        /** @var string */
        public $name;
        /** @var string */
        public $url;
    }
}

namespace EnterSite\Model\Page\DefaultLayout\Search {
    class Hint {
        /** @var string */
        public $name;
        /** @var string */
        public $url;
    }
}