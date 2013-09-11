<?php

return function(
    \Helper\TemplateHelper $helper,
    \Iterator\EntityPager $pager
) {
    /** @var \Model\Product\Entity $product */

    $user = \App::user();

    $products = [];
    foreach ($pager as $product) {
        $products[] = [
            'name'     => $product->getName(),
            'link'     => $product->getLink(),
            'cart'     => [
                'addLink' => $helper->url('cart.product.set', ['productId' => $product->getId()]),
            ],
            'image'    => $product->getImageUrl(2),
            'price'    => $helper->formatPrice($product->getPrice()),
            'oldPrice' => ($product->getPriceOld() && !$user->getRegion()->getHasTransportCompany())
                ? $helper->formatPrice($product->getPriceOld())
                : null
            ,
        ];
    }

    echo $helper->renderWithMustache('product/list/_compact', ['products' => $products]);
};