<?php

namespace EnterSite\Model\Page {
    use EnterSite\Model;

    class DefaultLayout extends Model\Page {
        /** @var mixed */
        public $content;
        /** @var Model\Page\DefaultLayout\RegionLink */
        public $regionLink;

        public function __construct() {
            parent::__construct();

            $this->regionLink = new DefaultLayout\RegionLink();
        }
    }
}

namespace EnterSite\Model\Page\DefaultLayout {
    class RegionLink {
        /** string */
        public $name;
        /** string */
        public $url;
    }
}