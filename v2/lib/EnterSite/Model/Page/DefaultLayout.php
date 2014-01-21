<?php

namespace EnterSite\Model\Page {
    use EnterSite\RouterTrait;
    use EnterSite\Model;
    use EnterSite\Routing;

    class DefaultLayout extends Model\Page {
        use RouterTrait;

        /** @var mixed */
        public $content;
        /** @var Model\Page\DefaultLayout\RegionLink */
        public $regionLink;

        protected function setRegionLink(Model\Region $region) {
            $link = new Model\Page\DefaultLayout\RegionLink();

            $link->name = $region->name;
            $link->url = $this->getRouter()->getUrlByRoute(new Routing\SetRegion($region));

            $this->regionLink = $link;
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