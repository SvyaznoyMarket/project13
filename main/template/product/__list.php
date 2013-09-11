<?php

return function(
    \Helper\TemplateHelper $helper,
    \Iterator\EntityPager $pager,
    array $productVideosByProduct
) {
    /** @var \Model\Product\Entity $product */

    $user = \App::user();

    $products = [];
    foreach ($pager as $product) {
        $productVideos = isset($productVideosByProduct[$product->getId()]) ? $productVideosByProduct[$product->getId()] : [];
        /** @var $productVideo \Model\Product\Video\Entity|null */
        $productVideo = reset($productVideos);
        /** @var string $model3dExternalUrl */
        $model3dExternalUrl = ($productVideo instanceof \Model\Product\Video\Entity) ? $productVideo->getMaybe3d() : null;
        /** @var string $model3dImg */
        $model3dImg = ($productVideo instanceof \Model\Product\Video\Entity) ? $productVideo->getImg3d() : null;

        $products[] = [
            'name'         => $product->getName(),
            'link'         => $product->getLink(),
            'label'        =>
                $product->getLabel()
                ? ['name' => $product->getLabel()->getName(), 'image' => $product->getLabel()->getImageUrl()]
                : null
            ,
            'cart'         => [
                'addLink' => $helper->url('cart.product.set', ['productId' => $product->getId()]),
            ],
            'image'        => $product->getImageUrl(2),
            'price'        => $helper->formatPrice($product->getPrice()),
            'oldPrice'     => ($product->getPriceOld() && !$user->getRegion()->getHasTransportCompany())
                ? $helper->formatPrice($product->getPriceOld())
                : null
            ,
            'onlyInShop'   => !$product->getIsBuyable() && $product->getState()->getIsShop(),
            'variations'   =>
                ((isset($hasModel) ? $hasModel : true) && $product->getModel() && (bool)$product->getModel()->getProperty())
                ? array_map(function(\Model\Product\Model\Property\Entity $property) {
                    return [
                        'name' => $property->getName(),
                    ];
                }, $product->getModel()->getProperty())
                : null
            ,
            'hasVideo' => $productVideo && $productVideo->getContent(),
            'has360'   => $model3dExternalUrl || $model3dImg,
        ];
    }

    echo $helper->renderWithMustache('product/list/_compact', ['products' => $products]);
};