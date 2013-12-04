<?php

return function (
    \Helper\TemplateHelper $helper,
    \Model\Product\BasicEntity $product,
    $url = null,
    $class = null,
    $value = 'Купить',
    $directLink = false
) {
    $class = \View\Id::cartButtonForProduct($product->getId()) . ' ' . $class;

    if (!$directLink) {
        $class .= $product->isInShopStockOnly() ? ' jsOneClickButton' : ' jsBuyButton';
    }

    if ($product->isInShopStockOnly()) {
        $class .= ' mShopsOnly';
        $value = 'Резерв';
        $url = $helper->url('cart.oneClick.product.set', ['productId' => $product->getId()]);
    } elseif ($product->isInShopShowroomOnly()) {
        $class .= ' mShopsOnly';
    }

    if (!$product->isInShopStockOnly() && false === strpos($class, 'jsBuyButton')) {
        $class .= ' jsBuyButton';
    }

    if (!$product->getIsBuyable()) {
        $url = '#';
        $class .= ' mDisabled';
        $value = $product->isInShopShowroomOnly() ? 'На витрине' : 'Нет в наличии';
    } else if (!isset($url)) {
        $urlParams = [
            'productId' => $product->getId(),
        ];
        if ($helper->hasParam('sender')) {
            $urlParams['sender'] = $helper->getParam('sender') . '|' . $product->getId();
        }
        $url = $helper->url('cart.product.set', $urlParams);
    }

    $upsaleData = [
        'url' => $helper->url('product.upsale', ['productId' => $product->getId()]),
        'fromUpsale' => ($helper->hasParam('from') && 'cart_rec' === $helper->getParam('from')) ? true : false,
    ];
    ?>
    <div class="bWidgetBuy__eBuy btnBuy">
        <a href="<?= $url ?>" class="<?= $class ?>" data-group="<?= $product->getId() ?>" data-upsale='<?= json_encode($upsaleData) ?>'><?= $value ?></a>
    </div>

<? };