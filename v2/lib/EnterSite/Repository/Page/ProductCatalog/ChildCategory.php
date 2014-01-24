<?php

namespace EnterSite\Repository\Page\ProductCatalog;

use EnterSite\RouterTrait;
use EnterSite\Routing;
use EnterSite\Repository;
use EnterSite\Model;
use EnterSite\Model\Partial;
use EnterSite\Model\Page\ProductCatalog\ChildCategory as Page;

class ChildCategory {
    use RouterTrait;

    /**
     * @param ChildCategory\Request $request
     * @return Page
     */
    public function getObjectByRequest(ChildCategory\Request $request) {
        $page = new Page();

        $page->styles[] = '/css/global.min.css';
        $page->styles[] = '/styles/global.min.css';

        // TODO: вынести в parent-класс
        if ($request->region) {
            $page->header->regionLink->name = $request->region->name;
            $page->header->regionLink->url = $this->getRouter()->getUrlByRoute(new Routing\SetRegion($request->region));
        }

        $productCardRepository = new Repository\Partial\ProductCard();
        $cartProductButtonRepository = new Repository\Partial\Cart\ProductButton();
        foreach ($request->products as $product) {
            $productCard = $productCardRepository->getObject($product, $cartProductButtonRepository->getObject($product));
            $page->content->productBlock->products[] = $productCard;
        }

        //die(json_encode($page, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        return $page;
    }
}