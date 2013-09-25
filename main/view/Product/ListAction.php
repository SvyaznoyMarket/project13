<?php

namespace View\Product;

class ListAction {
    /**
     * @param \Helper\TemplateHelper $helper
     * @param \Iterator\EntityPager $pager
     * @param array $productVideosByProduct
     * @return array
     */
    public function execute(
        \Helper\TemplateHelper $helper,
        \Iterator\EntityPager $pager,
        array $productVideosByProduct
    ) {
        /** @var \Model\Product\Entity $product */

        $user = \App::user();

        $productButtonAction = new \View\Cart\ProductButtonAction();
        $reviewCompactAction = new \View\Product\ReviewCompactAction();

        $productData = [];
        foreach ($pager as $product) {
            $productVideos = isset($productVideosByProduct[$product->getId()]) ? $productVideosByProduct[$product->getId()] : [];
            /** @var $productVideo \Model\Product\Video\Entity|null */
            $productVideo = reset($productVideos);
            /** @var string $model3dExternalUrl */
            $model3dExternalUrl = ($productVideo instanceof \Model\Product\Video\Entity) ? $productVideo->getMaybe3d() : null;
            /** @var string $model3dImg */
            $model3dImg = ($productVideo instanceof \Model\Product\Video\Entity) ? $productVideo->getImg3d() : null;

            $productItem = [
                'name'         => $product->getName(),
                'link'         => $product->getLink(),
                'label'        =>
                $product->getLabel()
                    ? ['name' => $product->getLabel()->getName(), 'image' => $product->getLabel()->getImageUrl()]
                    : null
                ,
                'cartButton'   => [],
                'image'        => $product->getImageUrl(2),
                'price'        => $helper->formatPrice($product->getPrice()),
                'oldPrice'     => ($product->getPriceOld() && !$user->getRegion()->getHasTransportCompany())
                    ? $helper->formatPrice($product->getPriceOld())
                    : null
                ,
                'isBuyable'    => $product->getIsBuyable(),
                'onlyInShop'   => $product->isInShopOnly(),
                'variations'   =>
                ((isset($hasModel) ? $hasModel : true) && $product->getModel() && (bool)$product->getModel()->getProperty()) // TODO: перенести в \View\*Action
                    ? array_map(function(\Model\Product\Model\Property\Entity $property) {
                    return [
                        'name' => $property->getName(),
                    ];
                }, $product->getModel()->getProperty())
                    : null
                ,
                'hasVideo' => $productVideo && $productVideo->getContent(),
                'has360'   => $model3dExternalUrl || $model3dImg,
                'review'   => $reviewCompactAction->execute($helper, $product),
                'line'     => $product->getLine() ? ['name' => $product->getLine()->getName(), 'productCount' => $product->getLine()->getProductCount(), 'kitCount' => $product->getLine()->getKitCount()] : null,
            ];

            // cart
            $productItem['cartButton'] = $productButtonAction->execute($helper, $product);

            $productData[] = $productItem;
        }

        return [
            'products' => $productData,
        ];
    }
}