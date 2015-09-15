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
     * @param \View\Product\ReviewCompactAction $reviewtAction
     * @param string $imageSourceType
     * @param array $cartButtonSender
     * @return array
     */
    public function execute(
        \Helper\TemplateHelper $helper,
        \Model\Product\Entity $product,
        $buyMethod = null,
        $showState = true,
        $cartButtonAction = null,
        $reviewtAction = null,
        $imageSourceType = 'product_160',
        array $cartButtonSender = [],
        \Model\Product\Category\Entity $category = null,
        \Model\Favorite\Product\Entity $favoriteProduct = null
    ) {
        $router = \App::router();

        if ($product->isInShopOnly()) {
            $inShopOnlyLabel = ['name' => 'Только в магазинах'];
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

        if (!$product->isInShopOnly() && $isFurniture && $product->getState() && $product->getState()->getIsStore() && !$product->getSlotPartnerOffer()) {
            $inStoreLabel = ['name' => 'Товар со склада', 'inStore' => true]; // SITE-3131
        } else {
            $inStoreLabel = null;
        }

        $variations = (new \View\Product\Variations())->execute($helper, $product);
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
            'showCompareButton' => !$product->getKit() || $product->getIsKitLocked(),
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
            'variations'   => $variations,
            'hasVariations' => $variations ? true : null,
            'hasVideo' => $product->hasVideo(),
            'has360'   => $product->has3d(),
            'review'   => $reviewtAction ? $reviewtAction->execute($helper, $product) : null,
            'isBanner' => false,
            'hasKit'       => (bool)$product->getKit(),
            'isKitLocked'   => (bool)$product->getIsKitLocked(),
            'brandImage'    => $product->getBrand() && $product->getBrand()->getImage() ? $product->getBrand()->getImage() : null,
            'isSlot' => (bool)$product->getSlotPartnerOffer(),
            'isOnlyFromPartner' => $product->isOnlyFromPartner(),
            'isNewWindow'       => \App::abTest()->isNewWindow(), // открытие товаров в новом окне
            'compareButton'     => [
                'id'                => $product->id,
                'typeId'            => $product->getType() ? $product->getType()->getId() : null,
                'addUrl'            => $router->generate('compare.add', ['productId' => $product->getId(), 'location' => 'product']),
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
        ];

        $productItem['properties'] = (new \View\Product\Properties())->execute($helper, $product);

        // oldPrice and priceSale
        if ( $product->getPriceOld() && $product->getLabel()) {
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