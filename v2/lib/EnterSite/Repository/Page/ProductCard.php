<?php

namespace EnterSite\Repository\Page;

use EnterSite\Routing;
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

        $productModel = $request->product;

        $page->content->product->title = $productModel->name;
        $page->content->product->article = $productModel->article;
        $page->content->product->description = $productModel->description;

        foreach ($productModel->media->photos as $photoModel) {
            $photo = new Page\Content\Product\Photo();
            $photo->name = $productModel->name;
            $photo->url = (string)(new Routing\Product\Media\GetPhoto($photoModel->source, $photoModel->id, 3));

            $page->content->product->photos[] = $photo;

            break; // FIXME: убрать заглушку
        }

        //die(json_encode($page, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }
}