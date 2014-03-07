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

        // фотографии товара
        foreach ($productModel->media->photos as $photoModel) {
            $photo = new Page\Content\Product\Photo();
            $photo->name = $productModel->name;
            $photo->url = (string)(new Routing\Product\Media\GetPhoto($photoModel->source, $photoModel->id, 3));

            $page->content->product->photos[] = $photo;
        }

        // характеристики товара
        $groupedPropertyModels = [];
        foreach ($productModel->properties as $propertyModel) {
            if (!isset($groupedPropertyModels[$propertyModel->groupId])) {
                $groupedPropertyModels[$propertyModel->groupId] = [];
            }

            $groupedPropertyModels[$propertyModel->groupId][] = $propertyModel;
        }

        foreach ($productModel->propertyGroups as $propertyGroupModel) {
            if (!isset($groupedPropertyModels[$propertyGroupModel->id][0])) continue;

            $propertyChunk = new Page\Content\Product\PropertyChunk();

            $property = new Page\Content\Product\PropertyChunk\Property();
            $property->isTitle = true;
            $property->name = $propertyGroupModel->name;
            $propertyChunk->properties[] = $property;

            foreach ($groupedPropertyModels[$propertyGroupModel->id] as $propertyModel) {
                /** @var Model\Product\Property $propertyModel */
                $property = new Page\Content\Product\PropertyChunk\Property();
                $property->isTitle = false;
                $property->name = $propertyModel->name;
                $property->value = $propertyModel->shownValue . ($propertyModel->unit ? (' ' . $propertyModel->unit) : '');
                $propertyChunk->properties[] = $property;
            }

            $page->content->product->propertyChunks[] = $propertyChunk;
        }

        // рейтинг товара
        if ($productModel->rating) {
            $rating = new Page\Content\Product\Rating();
            $rating->reviewCount = $productModel->rating->reviewCount;

            $score = $productModel->rating->starScore;
            for ($i = 0; $i < (int)$score; $i++) {
                $star = new Page\Content\Product\Rating\Star();
                $star->image = 'star.png';
                $rating->stars[] = $star;
            }
            if (ceil($score) > $score) {
                $star = new Page\Content\Product\Rating\Star();
                $star->image = 'starHalf.png';
                $rating->stars[] = $star;
            }
            for ($i = 5; $i > ceil($score); $i--) {
                $star = new Page\Content\Product\Rating\Star();
                $star->image = 'starEmpty.png';
                $rating->stars[] = $star;
            }

            $page->content->product->rating = $rating;
        }

        //die(json_encode($page, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }
}