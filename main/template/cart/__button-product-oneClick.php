<?php

return function (
    \Helper\TemplateHelper $helper,
    \Model\Product\Entity $product,
    $url = null,
    $class = null,
    $value = 'Купить быстро в 1 клик',
    \Model\Shop\Entity $shop = null,
    array $sender = [],
    $sender2 = ''
) {

    $data = [
        'id'        => sprintf('quickBuyButton-%s', $product->getId()),
        'productUi' => $product->getUi(),
        'shop'      => $shop,
        'url'       => $url,
        'text'      => $value,
        'sender'    => $helper->json($sender),
        'sender2'   => $sender2
    ];

    if (
        !$product->getIsBuyable()
        || (5 === $product->getStatusId()) // SITE-2924
    ) {
        return '';
    }

    $urlParams = [];

    if ($sender) {
        $urlParams = array_replace_recursive([
            'sender' => [
                'name'      => null,
                'position'  => null,
                'method'    => null,
                'from'      => null,
            ]
        ], ['sender' => $sender]);
    }

    if ($sender2) $urlParams['sender2'] = $sender2;

    if (!$product->getKit()) {
        $data['class'] = \View\Id::cartButtonForProduct($product->getId() . '-oneClick') . ' jsOneClickButton-new ' . $class;
    }

    if ($product->getIsBuyable() && $shop) {
        $data['class'] .= \Session\AbTest\AbTest::getColorClass($product);
    }

    if (!$product->getIsBuyable()) {
        $data['url'] = '#';
        $data['class'] .= ' mDisabled';
        $data['text'] = $product->isInShopShowroomOnly() ? 'На витрине' : 'Нет в наличии';
    } else if (!isset($url)) {
        $urlParams['productId'] = $product->getId();
        if ($helper->hasParam('sender'))  $urlParams['sender'] = $helper->getParam('sender') . '|' . $product->getId();
        $data['url'] = $helper->url('cart.oneClick.product.set', $urlParams);
    }

    if ($product->getKit() && !$product->getIsKitLocked()) {
        $data['url'] = $helper->url('cart.oneClick.product.setList', $urlParams);
    }

    echo \App::abTest()->isNewProductPage()
        ? $helper->renderWithMustache('product-page/_buyButtonOneClick', $data)
        : $helper->renderWithMustache('product/_buyButtonOneClick', $data);

};