<?php

namespace EnterSite\Repository\Page;

use EnterSite\RouterTrait;
use EnterSite\Routing;
use EnterSite\Repository;
use EnterSite\Model;
use EnterSite\Model\Page\DefaultLayout as Page;

class DefaultLayout {
    use RouterTrait;

    /**
     * @param Page $page
     * @param DefaultLayout\Request $request
     */
    public function buildObjectByRequest(Page $page, DefaultLayout\Request $request) {
        $page->styles[] = '/css/global.min.css';
        $page->styles[] = '/styles/global.min.css';

        // TODO: вынести в parent-класс
        if ($request->region) {
            $page->header->regionLink->name = $request->region->name;
            $page->header->regionLink->url = $this->getRouter()->getUrlByRoute(new Routing\Region\Set($request->region->id));
        }
    }
}