<?php

namespace View\Product;

class ShowAction {
    /**
     * @param \Helper\TemplateHelper $helper
     * @param \Model\Product\BasicEntity $product
     * @param null $buyMethod
     * @param bool $showState
     * @param \View\Cart\ProductButtonAction $cartButtonAction
     * @param \View\Product\ReviewCompactAction $reviewtAction
     * @param int $imageSize
     * @param array $cartButtonSender
     * @return array
     */
    public function execute(
        \Helper\TemplateHelper $helper,
        \Model\Product\BasicEntity $product,
        $buyMethod = null,
        $showState = true,
        $cartButtonAction = null,
        $reviewtAction = null,
        $imageSize = 2,
        array $cartButtonSender = []
    ) {
        /** @var $product \Model\Product\Entity */

        $user = \App::user();

        $stateLabel = null;
        if ($product->isInShopOnly()) {
            $stateLabel = ['name' => 'Только в магазинах'];
        } else if ($product->getMainCategory() && $product->getMainCategory()->getIsFurniture() && $product->getState() && $product->getState()->getIsStore() && !$product->getSlotPartnerOffer()) {
            if (\App::config()->region['defaultId'] === $user->getRegion()->getId()) {
                // Для Москвы, SITE-2850
                //$stateLabel = ['name' => 'Товар за три дня'];
                $stateLabel = ['name' => 'Товар со склада', 'inStore' => true]; // SITE-3131
            } else {
                // Для регионов (привозит быстрее, но не за три дня)
                $stateLabel = ['name' => 'Товар со склада', 'inStore' => true];
            }
            //$showState = true; // включаем отображение шильдика для всех
        }

        $productItem = [
            'id'           => $product->getId(),
            'name'         => $product->getName(),
            'link'         => $product->getLink(),
            'label'        =>
            $product->getLabel()
                ? ['name' => $product->getLabel()->getName(), 'image' => $product->getLabel()->getImageUrl()]
                : null
            ,
            'isPodariZhiznProduct' => $product->getLabel() && $product->getLabel()->getId() == \Model\Product\BasicEntity::LABEL_ID_PODARI_ZHIZN,
            'cartButton'   => [],
            'image'        => $product->getImageUrl($imageSize),
            'hoverImage'   => $this->getHoverImageUrl($product, $imageSize),
            'price'        => $helper->formatPrice($product->getPrice()),
            'oldPrice'     => null,
            'isBuyable'    => $product->getIsBuyable(),
            'isInShopShowroomOnly' => !$product->getIsBuyable() && $product->isInShopShowroomOnly(),
            'isInShopStockOnly'    => $product->isInShopStockOnly(),
            'onlyInShop'   => $product->isInShopOnly(),
            'stateLabel'   => $showState ? $stateLabel : null,
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
            'line'     =>
            ($line = $product->getLine())
                ? ['name' => $line->getName(), 'productCount' => $line->getLineCount(), 'link' => $helper->url('product.line', ['lineToken' => $line->getToken()])]
                : null,
            'hasKit'       => (bool)$product->getKit(),
            'isKitLocked'   => (bool)$product->getIsKitLocked(),
            'brandImage'    => $product->getBrand() && $product->getBrand()->getImage() ? $product->getBrand()->getImage() : null,
            'isSlot' => (bool)$product->getSlotPartnerOffer(),
            'isOnlyFromPartner' => $product->isOnlyFromPartner(),
        ];

        // oldPrice and priceSale
        if ( $product->getPriceOld() ) {
            $productItem['oldPrice'] = $helper->formatPrice($product->getPriceOld());
            $productItem['priceSale'] = round( ( 1 - ($product->getPrice() / $product->getPriceOld() ) ) *100, 0 );
        }

        // cart
        if ($buyMethod && in_array(strtolower($buyMethod), ['none', 'false'])) {
            $productItem['cartButton'] = null;
        } else {
            $productItem['cartButton'] = $cartButtonAction ? $cartButtonAction->execute($helper, $product, null, false, $cartButtonSender, false) : null;
        }

        if ($product->isGifteryCertificate()) $productItem['price'] = 'от ' . \App::config()->partners['Giftery']['lowestPrice'];

        return $productItem;
    }

    private function getHoverImageUrl(\Model\Product\Entity $product, $imageSize) {
        foreach ($product->getPhoto() as $photo) {
            if (40 == $photo->getPosition()) {
                return $photo->getUrl($imageSize);
            }
        }

        return null;
    }
}