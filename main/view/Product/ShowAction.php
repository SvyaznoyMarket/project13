<?php

namespace View\Product;

use Session\AbTest\AbTest;

class ShowAction {
    /**
     * @param \Helper\TemplateHelper $helper
     * @param \Model\Product\Entity $product
     * @param null $buyMethod
     * @param bool $showState
     * @param \View\Cart\ProductButtonAction $cartButtonAction
     * @param \View\Product\ReviewCompactAction $reviewAction
     * @param string $imageSourceType
     * @param array $cartButtonSender
     * @param \Model\Product\Category\Entity|null $category
     * @param \Model\Favorite\Product\Entity|null $favoriteProduct
     * @param int|string $categoryView
     * @return array
     */
    public function execute(
        \Helper\TemplateHelper $helper,
        \Model\Product\Entity $product,
        $buyMethod = null,
        $showState = true,
        $cartButtonAction = null,
        $reviewAction = null,
        $imageSourceType = 'product_160',
        array $cartButtonSender = [],
        \Model\Product\Category\Entity $category = null,
        \Model\Favorite\Product\Entity $favoriteProduct = null,
        $categoryView = \Model\Product\Category\Entity::VIEW_COMPACT
    ) {
        $router = \App::router();

        if ($product->isInShopOnly()) {
            $inShopOnlyLabel = ['name' => 'Только в cENTER'];
        } else {
            $inShopOnlyLabel = null;
        }

        $isFurniture = false;
        call_user_func(function() use(&$product, &$isFurniture) {
            foreach ($product->categories as $category) {
                if ($category->getRootOfParents()->getIsFurniture()) {
                    $isFurniture = true;
                    break;
                }
            }
        });

        if (
            !$product->isInShopOnly()
            && $isFurniture
            && $product->getState() && $product->getState()->getIsStore()
            && !$product->getSlotPartnerOffer()
            && (!$category || !$category->getClosestFromAncestors(['b8569e65-e31e-47a1-af20-5b06aff9f189'])) // SITE-6460
        ) {
            $inStoreLabel = ['name' => 'Товар со склада', 'inStore' => true]; // SITE-3131
        } else {
            $inStoreLabel = null;
        }

        $productItem = [
            'id'           => $product->id,
            'ui'           => $product->ui,
            'name'         => $product->getName(),
            'link'         => $product->getLink(),
            'label'        =>
            $product->getLabel()
                ? ['name' => $product->getLabel()->getName(), 'image' => $product->getLabel()->getImageUrl()]
                : null
            ,
            'showCartButton' => true,
            'showCompareButton' => (!$product->getKit() || $product->getIsKitLocked()) && !$product->isGifteryCertificate(),
            'cartButton'   => [],
            'image'        => $product->getMainImageUrl($imageSourceType),
            'hoverImage'   => $product->getHoverImageUrl($imageSourceType),
            'price'        => $helper->formatPrice($product->getPrice()),
            'oldPrice'     => null,
            'isBuyable'    => $product->getIsBuyable(),
            'inShopShowroomLabel'  => !$product->getIsBuyable() && $product->isInShopShowroomOnly() ? 'На витрине' : '',
            'inShopStockOnlyLabel' => $product->isInShopStockOnly() ? 'Только в магазинах' : '',
            'notBuyableLabel'      => !$product->isInShopShowroomOnly() && !$product->isInShopStockOnly() && !$product->getIsBuyable() ? 'Нет в наличии' : '',
            'inStoreLabel' => $inStoreLabel,
            'onlyInShop'   => $product->isInShopOnly(),
            'stateLabel'   => $showState ? ($inShopOnlyLabel ? $inShopOnlyLabel : $inStoreLabel) : null,
            'variations'   => (new \View\Category\Listing\Product\Variations())->execute($helper, $product, $category ? $category->ui : '', $cartButtonSender, $categoryView),
            'hasVideo' => $product->hasVideo(),
            'has360'   => $product->has3d(),
            'review'   => $reviewAction ? $reviewAction->execute($helper, $product) : null,
            'isBanner' => false,
            'hasKit'       => (bool)$product->getKit(),
            'isKitLocked'   => (bool)$product->getIsKitLocked(),
            'brandImage'    => $product->getBrand() && $product->getBrand()->isTchibo() ? $product->getBrand()->getImage() : null,
            'isSlot' => (bool)$product->getSlotPartnerOffer(),
            'isOnlyFromPartner' => $product->isOnlyFromPartner(),
            'compareButton'     => [
                'id'                => $product->id,
                'typeId'            => $product->getType() ? $product->getType()->getId() : null,
                'addUrl'            => $router->generateUrl('compare.add', ['productId' => $product->getId(), 'location' => 'product']),
                'isSlot'            => (bool)$product->getSlotPartnerOffer(),
                'isOnlyFromPartner' => $product->isOnlyFromPartner(),
            ],
            'favoriteButton'     =>
                [
                    'ui' => $product->ui,
                ]
                + (
                    $favoriteProduct && $favoriteProduct->isFavourite
                    ? [
                        'isInFavorite' => true,
                        'url'          => $helper->url('favorite.delete', ['productUi' => $product->getUi()]),
                        'text'         => 'Убрать из избранного',
                    ]
                    : [
                        'isInFavorite' => false,
                        'url'          => $helper->url('favorite.add', ['productUi' => $product->getUi()]),
                        'text'         => 'В избранное',
                    ]
                )
            ,
            'ecommerce' => $product->ecommerceData(),
            'isCompact' => $categoryView == \Model\Product\Category\Entity::VIEW_COMPACT,
            'isExpanded' => $categoryView == \Model\Product\Category\Entity::VIEW_EXPANDED,
            'isLight' => in_array($categoryView, [
                \Model\Product\Category\Entity::VIEW_LIGHT_WITH_BOTTOM_DESCRIPTION,
                \Model\Product\Category\Entity::VIEW_LIGHT_WITH_HOVER_BOTTOM_DESCRIPTION,
                \Model\Product\Category\Entity::VIEW_LIGHT_WITHOUT_DESCRIPTION,
            ]),
        ];

        switch ($categoryView) {
            case \Model\Product\Category\Entity::VIEW_LIGHT_WITH_BOTTOM_DESCRIPTION:
                $productItem['isWithExtraContent'] = true;
                $productItem['isWithBottomDescription'] = true;
                $productItem['isWithHoverDescription'] = false;
                break;
            case \Model\Product\Category\Entity::VIEW_LIGHT_WITH_HOVER_BOTTOM_DESCRIPTION:
                $productItem['isWithExtraContent'] = true;
                $productItem['isWithBottomDescription'] = true;
                $productItem['isWithHoverDescription'] = true;
                break;
            case \Model\Product\Category\Entity::VIEW_LIGHT_WITHOUT_DESCRIPTION:
                $productItem['isWithExtraContent'] = false;
                $productItem['isWithBottomDescription'] = false;
                $productItem['isWithHoverDescription'] = false;
                break;
        }

        $productItem['properties'] = (new \View\Product\Properties())->execute($helper, $product);

        if ($category && $category->isTchibo()) {
            $productItem['brandImage'] = null;
        }

        // oldPrice and priceSale
        if ($product->getPriceOld()) {
            $productItem['oldPrice'] = $helper->formatPrice($product->getPriceOld());
            if (AbTest::isCurrencyDiscountPrice()) {
                $productItem['priceSale'] = $helper->formatPrice($product->getPriceOld() - $product->getPrice());
                $productItem['priceSaleUnit'] = ' <span class="rubl">p</span>';
            } else {
                $productItem['priceSale'] = round((1 - ($product->getPrice() / $product->getPriceOld())) * 100, 0);
                $productItem['priceSaleUnit'] = '%';
            }
            $productItem['showPriceSale'] = AbTest::isShowSalePercentage();
        }

        // cart
        if ($buyMethod && in_array(strtolower($buyMethod), ['none', 'false'])) {
            $productItem['cartButton'] = null;
        } else {
            $productItem['cartButton'] = $cartButtonAction ? $cartButtonAction->execute($helper, $product, null, $cartButtonSender, false) : null;
        }

        if ($product->isGifteryCertificate()) $productItem['price'] = 'от ' . \App::config()->partners['Giftery']['lowestPrice'];

        return $productItem;
    }
}