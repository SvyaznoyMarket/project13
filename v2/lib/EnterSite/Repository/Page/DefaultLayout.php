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
        $page->styles[] = '/v2/css/global.css';

        $page->title = 'Enter - все товары для жизни по интернет ценам!';

        $page->mainMenu = $request->mainMenu;
    }
}