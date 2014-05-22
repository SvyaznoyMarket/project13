<?php

namespace EnterSite\Repository\Page\ProductCatalog;

use EnterSite\ConfigTrait;
use EnterSite\RouterTrait;
use EnterSite\ViewHelperTrait;
use EnterSite\Routing;
use EnterSite\Repository;
use EnterSite\Model;
use EnterSite\Model\Partial;
use EnterSite\Model\Page\ProductCatalog\ChildCategory as Page;

class ChildCategory {
    use ConfigTrait, RouterTrait, ViewHelperTrait {
        ConfigTrait::getConfig insteadof RouterTrait, ViewHelperTrait;
    }

    /**
     * @param Page $page
     * @param ChildCategory\Request $request
     */
    public function buildObjectByRequest(Page $page, ChildCategory\Request $request) {
        (new Repository\Page\DefaultLayout)->buildObjectByRequest($page, $request);

        $config = $this->getConfig();
        $router = $this->getRouter();
        $viewHelper = $this->getViewHelper();

        $productCardRepository = new Repository\Partial\ProductCard();
        $cartProductButtonRepository = new Repository\Partial\Cart\ProductButton();
        $ratingRepository = new Repository\Partial\Rating();

        $page->dataModule = 'product.catalog';

        $page->content->productBlock = false;
        if ((bool)$request->products) {
            $page->content->productBlock = new Page\Content\ProductBlock();
            $page->content->productBlock->limit = $config->product->itemPerPage;
            $page->content->productBlock->url = $router->getUrlByRoute(new Routing\Product\GetListByFilter());
            $page->content->productBlock->dataValue = $viewHelper->json([
                'limit'      => $page->content->productBlock->limit,
                'offset'     => 0,
                'f-category' => $request->category->id,
                'sort'       => null,
            ]);

            foreach ($request->products as $productModel) {
                $productCard = $productCardRepository->getObject($productModel, $cartProductButtonRepository->getObject($productModel));
                // рейтинг товара
                if ($productModel->rating) {
                    $rating = new Partial\Rating();
                    $rating->reviewCount = $productModel->rating->reviewCount;
                    $rating->stars = $ratingRepository->getStarList($productModel->rating->starScore);

                    $productCard->rating = $rating;
                }

                $page->content->productBlock->products[] = $productCard;
            }
        }

        //die(json_encode($page, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }
}