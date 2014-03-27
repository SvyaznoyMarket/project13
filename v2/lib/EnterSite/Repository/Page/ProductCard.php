<?php

namespace EnterSite\Repository\Page;

use EnterSite\RouterTrait;
use EnterSite\DateHelperTrait;
use EnterSite\TranslateHelperTrait;
use EnterSite\Routing;
use EnterSite\Repository;
use EnterSite\Model;
use EnterSite\Model\Partial;
use EnterSite\Model\Page\ProductCard as Page;

class ProductCard {
    use RouterTrait, DateHelperTrait, TranslateHelperTrait;

    /**
     * @param Page $page
     * @param ProductCard\Request $request
     */
    public function buildObjectByRequest(Page $page, ProductCard\Request $request) {
        (new Repository\Page\DefaultLayout)->buildObjectByRequest($page, $request);

        $router = $this->getRouter();
        $dateHelper = $this->getDateHelper();
        $translateHelper = $this->getTranslateHelper();
        $cartProductButtonRepository = new Repository\Partial\Cart\ProductButton();
        $productCardRepository = new Repository\Partial\ProductCard();
        $ratingRepository = new Repository\Partial\Rating();

        $productModel = $request->product;

        // хлебные крошки
        if ($categoryModel = $productModel->category) {
            $page->breadcrumbBlock = new Model\Page\DefaultLayout\BreadcrumbBlock();

            $breadcrumb = new Model\Page\DefaultLayout\BreadcrumbBlock\Breadcrumb();
            $breadcrumb->name = $categoryModel->name;
            $breadcrumb->url = $categoryModel->link;

            $page->breadcrumbBlock->breadcrumbs[] = $breadcrumb;
        }

        // содержание
        $page->content->product->title = $productModel->name;
        $page->content->product->article = $productModel->article;
        $page->content->product->description = $productModel->description;
        $page->content->product->price = $productModel->price;
        $page->content->product->shownPrice = $productModel->price ? number_format((float)$productModel->price, 0, ',', ' ') : null;
        $page->content->product->oldPrice = $productModel->oldPrice;
        $page->content->product->shownOldPrice = $productModel->oldPrice ? number_format((float)$productModel->oldPrice, 0, ',', ' ') : null;
        $page->content->product->cartButton = $cartProductButtonRepository->getObject($productModel);

        // доставка товара
        if ((bool)$productModel->nearestDeliveries) {
            $page->content->product->deliveryBlock = new Page\Content\Product\DeliveryBlock();
            foreach ($productModel->nearestDeliveries as $deliveryModel) {
                $delivery = new Page\Content\Product\DeliveryBlock\Delivery();

                if (Model\Product\NearestDelivery::TOKEN_STANDARD == $deliveryModel->token) {
                    $delivery->name = 'Доставка';
                } else if (Model\Product\NearestDelivery::TOKEN_SELF == $deliveryModel->token) {
                    $delivery->name = 'Самовывоз';
                } else if (Model\Product\NearestDelivery::TOKEN_NOW == $deliveryModel->token) {
                    $delivery->deliveredAtText = 'Сегодня есть в магазинах';
                } else {
                    continue;
                }

                if (in_array($deliveryModel->token, [Model\Product\NearestDelivery::TOKEN_STANDARD, Model\Product\NearestDelivery::TOKEN_SELF])) {
                    $delivery->priceText = !$deliveryModel->price
                        ? 'бесплатно'
                        : (number_format((float)$deliveryModel->price, 0, ',', ' ') . ' p')
                    ;
                    if ($deliveryModel->deliveredAt) {
                        $delivery->deliveredAtText = $translateHelper->humanizeDate($deliveryModel->deliveredAt);
                    }
                }

                $delivery->token = $deliveryModel->token;

                if (Model\Product\NearestDelivery::TOKEN_NOW == $deliveryModel->token) {
                    $delivery->hasShops = true;
                    foreach ($deliveryModel->shopsById as $shopModel) {
                        if (!$shopModel->region) continue;

                        $shop = new Page\Content\Product\DeliveryBlock\Delivery\Shop();
                        $shop->name = $shopModel->name;
                        $shop->url = $router->getUrlByRoute(new Routing\ShopCard\Get($shopModel->token, $shopModel->region->token));

                        $delivery->shops[] = $shop;
                    }
                }

                $page->content->product->deliveryBlock->deliveries[] = $delivery;
            }
        }

        // фотографии товара
        foreach ($productModel->media->photos as $photoModel) {
            $photo = new Page\Content\Product\Photo();
            $photo->name = $productModel->name;
            $photo->url = (string)(new Routing\Product\Media\GetPhoto($photoModel->source, $photoModel->id, 3));

            $page->content->product->photos[] = $photo;
        }

        // видео товара
        if ((bool)$productModel->media->videos) {
            $page->content->product->hasVideo = true;
            foreach ($productModel->media->videos as $videoModel) {
                $video = new Page\Content\Product\Video();
                $video->content = $videoModel->content;

                $page->content->product->videos[] = $video;
            }
        }

        // 3d фото товара (maybe3d)
        if ((bool)$productModel->media->photo3ds) {
            $page->content->product->hasPhoto3d = true;
            foreach ($productModel->media->photo3ds as $photo3dModel) {
                $photo3d = new Page\Content\Product\Photo3d();
                $photo3d->source = $photo3dModel->source;

                $page->content->product->photo3ds[] = $photo3d;
            }
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
            $rating->stars = $ratingRepository->getStarList($productModel->rating->starScore);

            $page->content->product->rating = $rating;
        }

        // аксессуары товара
        if ((bool)$productModel->relation->accessories) {
            $page->content->product->accessorySlider = new Partial\ProductSlider();
            foreach ($productModel->relation->accessories as $accessoryModel) {
                $page->content->product->accessorySlider->productCards[] = $productCardRepository->getObject($accessoryModel, $cartProductButtonRepository->getObject($productModel));
            }

            foreach ($request->accessoryCategories as $categoryModel) {
                $category = new Partial\ProductSlider\Category();
                $category->id = $categoryModel->id;
                $category->name = $categoryModel->name;

                $page->content->product->accessorySlider->categories[] = $category;
            }
            if ((bool)$page->content->product->accessorySlider->categories) {
                $page->content->product->accessorySlider->hasCategories = true;

                $category = new Partial\ProductSlider\Category();
                $category->id = '0';
                $category->name = 'Популярные аксессуары';

                array_unshift($page->content->product->accessorySlider->categories, $category);
            }
        }

        // отзывы товара
        if ((bool)$request->reviews) {
            $page->content->product->reviewBlock = new Page\Content\Product\ReviewBlock();
            foreach ($request->reviews as $reviewModel) {
                $review = new Page\Content\Product\ReviewBlock\Review();
                $review->author = $reviewModel->author;
                $review->createdAt = $reviewModel->createdAt ? $dateHelper->dateToRu($reviewModel->createdAt): null;
                $review->extract = $reviewModel->extract;
                $review->cons = $reviewModel->cons;
                $review->pros = $reviewModel->pros;
                $review->stars = $ratingRepository->getStarList($reviewModel->starScore);

                $page->content->product->reviewBlock->reviews[] = $review;
            }
        }

        // модели товара
        if ((bool)$productModel->model && (bool)$productModel->model->properties) {
            $page->content->product->hasModel = true;

            foreach ([
                 0 => [0, 1], // первое свойство модели
                 1 => [1, count($productModel->properties) - 1] // остальные свойства модели (будут скрыты по умолчанию)
            ] as $i => $range) {
                $modelBlock = new Page\Content\Product\ModelBlock();
                foreach (array_slice($productModel->model->properties, $range[0], $range[1]) as $propertyModel) {
                    /** @var Model\Product\ProductModel\Property $propertyModel */
                    $property = new Page\Content\Product\ModelBlock\Property();
                    //$property->name = !$propertyModel->isImage ? $propertyModel->name : null;
                    $property->name = $propertyModel->name;
                    $property->isImage = $propertyModel->isImage;
                    foreach ($propertyModel->options as $optionModel) {
                        $option = new Page\Content\Product\ModelBlock\Property\Option();
                        $option->isActive = false; // FIXME
                        $option->url = $optionModel->product ? $optionModel->product->link : null;
                        $option->shownValue = $optionModel->value;
                        $option->image = ($propertyModel->isImage && $optionModel->product)
                            ? (string)(new Routing\Product\Media\GetPhoto($optionModel->product->image, $optionModel->product->id, 2))
                            : null
                        ;

                        $property->options[] = $option;
                    }

                    $modelBlock->properties[] = $property;
                }

                if (!(bool)$modelBlock->properties) continue;

                if (0 === $i) {
                    $page->content->product->modelBlock = $modelBlock;
                } else if (1 === $i) {
                    $page->content->product->moreModelBlock = $modelBlock;
                }
            }
        }

        //die(json_encode($page, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }
}