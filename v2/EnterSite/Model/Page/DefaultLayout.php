<?php

namespace EnterSite\Model\Page {
    use EnterSite\RouterTrait;
    use EnterSite\Model\Page;
    use EnterSite\Model\Region;
    use EnterSite\Routing\SetRegion as SetRegionRoute;

    class DefaultLayout extends Page {
        use RouterTrait;

        /** @var RegionLink */
        public $regionLink;

        protected function setRegionLink(Region $region) {
            $link = new RegionLink();

            $link->name = $region->name;
            $link->url = $this->getRouter()->getUrlByRoute(new SetRegionRoute($region));

            $this->regionLink = $link;
        }
    }
}

namespace EnterSite\Model\Page {
    class RegionLink {
        /** string */
        public $name;
        /** string */
        public $url;
    }
}