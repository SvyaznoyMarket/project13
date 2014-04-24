<?php

return function (
    \Helper\TemplateHelper $helper,
    \Model\Product\BasicEntity $product,
    $url = null,
    $class = null,
    $value = 'Купить',
    $directLink = false,
    $onClick = null
) {
    /** @var $region \Model\Region\Entity|null */
    $region = \App::user()->getRegion();
    $forceDefaultBuy = $region ? $region->getForceDefaultBuy() : true;

    $class = \View\Id::cartButtonForProduct($product->getId()) . ' ' . $class;

    if (!$directLink) {
        $class .= $product->isInShopStockOnly() && $forceDefaultBuy ? ' jsOneClickButton' : ' jsBuyButton';
    }

    if ($product->isInShopStockOnly() && $forceDefaultBuy) {
        $class .= ' mShopsOnly';
        $value = 'Резерв';
        $url = $helper->url('cart.oneClick.product.set', ['productId' => $product->getId()]);
        $onClick = null;
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
        $onClick = null;
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
        <a href="<?= $url ?>" class="<?= $class ?>" data-group="<?= $product->getId() ?>" data-upsale='<?= json_encode($upsaleData) ?>'<? if (!empty($onClick)): ?> onclick="<?= $onClick ?>" <? endif ?>><?= $value ?></a>
    </div>

<? };