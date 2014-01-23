<?php

namespace EnterSite\Repository\Page\ProductCatalog;

use EnterSite\RouterTrait;
use EnterSite\Routing;
use EnterSite\Model;
use EnterSite\Model\Page\ProductCatalog\ChildCategory as Page;

class ChildCategory {
    use RouterTrait;

    /**
     * @param ChildCategory\Request $request
     * @return Page
     */
    public function getObjectByRequest(ChildCategory\Request $request) {
        $page = new Page();

        // TODO: вынести в parent-класс
        if ($request->region) {
            $page->regionLink->name = $request->region->name;
            $page->regionLink->url = $this->getRouter()->getUrlByRoute(new Routing\SetRegion($request->region));
        }

        foreach ($request->products as $product) {
            $productCard = new Page\Content\ProductBlock\ProductCard();
            $productCard->name = $product->name;
            $productCard->url = $product->link;

            $page->content->productBlock->products[] = $productCard;
        }

        //die(json_encode($page, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        return $page;
    }
}