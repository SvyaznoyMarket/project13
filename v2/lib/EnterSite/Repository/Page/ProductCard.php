<?php

namespace EnterSite\Repository\Page;

use EnterSite\Repository;
use EnterSite\Model;
use EnterSite\Model\Partial;
use EnterSite\Model\Page\ProductCard as Page;

class ProductCard {
    /**
     * @param Page $page
     * @param ProductCard\Request $request
     */
    public function buildObjectByRequest(Page $page, ProductCard\Request $request) {
        (new Repository\Page\DefaultLayout)->buildObjectByRequest($page, $request);

        //die(json_encode($page, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }
}