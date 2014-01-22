<?php

namespace EnterSite\Repository\Page\ProductCatalog;

use EnterSite\Model;

class ChildCategory {
    public function getObjectByRequest(ChildCategory\Request $request) {
        // страница
        $page = new Model\Page\ProductCatalog\ChildCategory();
        //die(json_encode($page, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        return $page;
    }
}