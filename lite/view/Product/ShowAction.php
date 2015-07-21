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
        array $cartButtonSender = []
    ) {
        $user = \App::user();

        if ($product->isInShopOnly()) {
            $inShopOnlyLabel = ['name' => 'Только в магазинах'];
        } else {
            $inShopOnlyLabel = null;
        }

        if (!$product->isInShopOnly() && $product->getRootCategory() && $product->getRootCategory()->getIsFurniture() && $product->getState() && $product->getState()->getIsStore() && !$product->getSlotPartnerOffer()) {
            $inStoreLabel = ['name' => 'Товар со склада', 'inStore' => true]; // SITE-3131
        } else {
            $inStoreLabel = null;
        }

        $productItem = [
            'id'           => $product->getId(),
            'name'         => htmlspecialchars_decode($product->getName()),
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
            'variations'   =>
                ((isset($hasModel) ? $hasModel : true) && $product->getModel() && (bool)$product->getModel()->getProperty()) // TODO: перенести в \View\*Action
                    ? array_map(function(\Model\Product\Model\Property\Entity $property) {
                    return [
                        'name' => $property->getName(),
                    ];
                }, $product->getModel()->getProperty())
                    : null
            ,
            'hasVariations' =>
                ((isset($hasModel) ? $hasModel : true) && $product->getModel() && (bool)$product->getModel()->getProperty())
                    ? true
                    : null
            ,
            'hasVideo' => $product->hasVideo(),
            'has360'   => $product->has3d(),
            'review'   => $reviewtAction ? $reviewtAction->execute($helper, $product) : null,
            'isBanner' => false,
            'hasKit'       => (bool)$product->getKit(),
            'isKitLocked'   => (bool)$product->getIsKitLocked(),
            'brandImage'    => $product->getBrand() && $product->getBrand()->getImage() ? $product->getBrand()->getImage() : null,
            'isSlot' => (bool)$product->getSlotPartnerOffer(),
            'isOnlyFromPartner' => $product->isOnlyFromPartner(),
            'lite'              => true,
            'buyButtonHtml'     => $helper->render('product/_button.buy', ['product' => $product, 'class' => 'btn-primary_middle' ])
        ];

        // oldPrice and priceSale
        if ( $product->getPriceOld() && $product->getLabel()) {
            $productItem['oldPrice'] = $helper->formatPrice($product->getPriceOld());
            $productItem['priceSale'] = round( ( 1 - ($product->getPrice() / $product->getPriceOld() ) ) *100, 0 );
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