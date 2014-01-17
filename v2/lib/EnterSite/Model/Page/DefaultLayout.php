<?php

namespace EnterSite\Model\Page {
    use EnterSite\RouterTrait;
    use EnterSite\Model\Page;
    use EnterSite\Model\Region;
    use EnterSite\Routing\SetRegion as SetRegionRoute;

    class DefaultLayout extends Page {
        use RouterTrait;

        /** @var mixed */
        public $content;
        /** @var Page\DefaultLayout\RegionLink */
        public $regionLink;

        protected function setRegionLink(Region $region) {
            $link = new Page\DefaultLayout\RegionLink();

            $link->name = $region->name;
            $link->url = $this->getRouter()->getUrlByRoute(new SetRegionRoute($region));

            $this->regionLink = $link;

            $this->content = 'Undefined';
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