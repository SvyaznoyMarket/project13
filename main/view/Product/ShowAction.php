<?php

namespace View\Product;

class ShowAction {
    /**
     * @param \Helper\TemplateHelper $helper
     * @param \Model\Product\BasicEntity $product
     * @param array $productVideosByProduct
     * @param null $buyMethod
     * @param bool $showState
     * @param \View\Cart\ProductButtonAction $cartButtonAction
     * @param \View\Product\ReviewCompactAction $reviewtAction
     * @param int $imageSize
     * @return array
     */
    public function execute(
        \Helper\TemplateHelper $helper,
        \Model\Product\BasicEntity $product,
        array $productVideosByProduct,
        $buyMethod = null,
        $showState = true,
        $cartButtonAction = null,
        $reviewtAction = null,
        $imageSize = 2
    ) {
        /** @var $product \Model\Product\Entity */

        $user = \App::user();

        $productVideos = isset($productVideosByProduct[$product->getId()]) ? $productVideosByProduct[$product->getId()] : [];
        /** @var $productVideo \Model\Product\Video\Entity|null */
        $productVideo = reset($productVideos);
        /** @var string $model3dExternalUrl */
        $model3dExternalUrl = ($productVideo instanceof \Model\Product\Video\Entity) ? $productVideo->getMaybe3d() : null;
        /** @var string $model3dImg */
        $model3dImg = ($productVideo instanceof \Model\Product\Video\Entity) ? $productVideo->getImg3d() : null;

        $stateLabel = null;
        if ($product->isInShopOnly()) {
            $stateLabel = ['name' => 'Только в магазинах'];
        } else if (
            $product->getMainCategory() && $product->getMainCategory()->getIsFurniture()
            && $product->getState() && $product->getState()->getIsStore()
        ) {
            if (\App::config()->region['defaultId'] === $user->getRegion()->getId()) {
                // Для Москвы, SITE-2850
                //$stateLabel = ['name' => 'Товар за три дня'];
                $stateLabel = ['name' => 'Товар со склада']; // SITE-3131
            } else {
                // Для регионов (привозит быстрее, но не за три дня)
                $stateLabel = ['name' => 'Товар со склада'];
            }
            //$showState = true; // включаем отображение шильдика для всех
        }

        $productItem = [
            'name'         => $product->getName(),
            'link'         => $product->getLink(),
            'label'        =>
            $product->getLabel()
                ? ['name' => $product->getLabel()->getName(), 'image' => $product->getLabel()->getImageUrl()]
                : null
            ,
            'cartButton'   => [],
            'image'        => $product->getImageUrl($imageSize),
            'price'        => $helper->formatPrice($product->getPrice()),
            'oldPrice'     => null,
            'isBuyable'    => $product->getIsBuyable(),
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
            'hasVideo' => $productVideo && $productVideo->getContent(),
            'has360'   => $model3dExternalUrl || $model3dImg,
            'review'   => $reviewtAction ? $reviewtAction->execute($helper, $product) : null,
            'isBanner' => false,
            'line'     =>
            ($line = $product->getLine())
                ? ['name' => $line->getName(), 'productCount' => $line->getLineCount(), 'link' => $helper->url('product.line', ['lineToken' => $line->getToken()])]
                : null,
            'hasKit'       => (bool)$product->getKit(),
            'isKitLocked'   => (bool)$product->getIsKitLocked(),
            'brandImage'    => $product->getBrand() && $product->getBrand()->getImage() ? $product->getBrand()->getImage() : null
        ];

        // oldPrice and priceSale
        if ( $product->getPriceOld() && !$user->getRegion()->getHasTransportCompany() ) {
            $productItem['oldPrice'] = $helper->formatPrice($product->getPriceOld());
            $productItem['priceSale'] = round( ( 1 - ($product->getPrice() / $product->getPriceOld() ) ) *100, 0 );
        }

        // cart
        if ($buyMethod && in_array(strtolower($buyMethod), ['none', 'false'])) {
            $productItem['cartButton'] = null;
        } else {
            $productItem['cartButton'] = $cartButtonAction ? $cartButtonAction->execute($helper, $product) : null;
        }

        return $productItem;
    }
}