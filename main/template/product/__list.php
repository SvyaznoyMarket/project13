<?php

return function(
    \Helper\TemplateHelper $helper,
    \Iterator\EntityPager $pager,
    array $productVideosByProduct
) {
    /** @var \Model\Product\Entity $product */

    $user = \App::user();

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

        // cart
        if (!$product->getIsBuyable()) {
            $productItem['cartButton']['url'] = '#';

            if (!$product->getIsBuyable() && $product->getState()->getIsShop()) {
                $productItem['cartButton']['value'] = 'Только в магазинах';
            } else {
                $productItem['cartButton']['value'] = 'Нет в наличии';
            }
        } else if (!isset($url)) {
            $urlParams = [
                'productId' => $product->getId(),
            ];
            if ($helper->hasParam('sender')) {
                $urlParams['sender'] = $helper->getParam('sender') . '|' . $product->getId();
            }
            $productItem['cartButton']['url'] = $helper->url('cart.product.set', $urlParams);
            $productItem['cartButton']['value'] = 'Купить';
        }

        $productData[] = $productItem;
    }

    echo $helper->renderWithMustache('product/list/_compact', ['products' => $productData]);
};