<?php

namespace EnterSite\Controller\ProductCatalog;

use Enter\Http;
use EnterSite\ConfigTrait;
use EnterSite\CurlClientTrait;
use EnterSite\MustacheRendererTrait;
use EnterSite\Controller;
use EnterSite\Repository;
use EnterSite\Curl\Query;
use EnterSite\Model;
//use EnterSite\Model\Page\ProductCatalog\RootCategory as Page;

class RootCategory {
    use ConfigTrait, CurlClientTrait, MustacheRendererTrait {
        ConfigTrait::getConfig insteadof CurlClientTrait, MustacheRendererTrait;
    }

    /**
     * @param Http\Request $request
     * @return Http\Response
     */
    public function execute(Http\Request $request) {
        // FIXME: заглушка
        return (new Controller\ProductCatalog\ChildCategory())->execute($request);
    }
}