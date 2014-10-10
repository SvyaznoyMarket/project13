<?php

return function (
    \Helper\TemplateHelper $helper,
    \Model\Product\BasicEntity $product
) {
    /** @var $region \Model\Region\Entity|null */
    $region = \App::user()->getRegion();
    $forceDefaultBuy = $region ? $region->getForceDefaultBuy() : true;

    $url = null;
    $value = 'Купить';
    $class = \View\Id::cartButtonForProduct($product->getId()) . ' btnBuy__eLink jsChangePackageSet';

    if ($product->isInShopStockOnly() && $forceDefaultBuy) {
        $class .= ' mShopsOnly';
        $value = 'Резерв';
        $url = $helper->url('cart.oneClick.product.set', ['productId' => $product->getId()]);
    } elseif ($product->isInShopShowroomOnly()) {
        $class .= ' mShopsOnly';
    }

    if (5 === $product->getStatusId()) { // SITE-2924
        return '';
    } else if (!$product->getIsBuyable()) {
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

    if ($product->getKit()) {
        $urlParams = [];
        foreach ($product->getKit() as $kitItem) {
            $urlParams['product'][] = ['id' => $kitItem->getId(), 'quantity' => $kitItem->getCount()];
        }
        $url = $helper->url('cart.product.setList', $urlParams);
    }

    $upsaleData = [
        'url' => $helper->url('product.upsale', ['productId' => $product->getId()]),
        'fromUpsale' => ($helper->hasParam('from') && 'cart_rec' === $helper->getParam('from')) ? true : false,
    ];
    ?>
    <div class="bWidgetBuy__eBuy btnBuy mBuySet">
        <a href="<?= $url ?>" class="<?= $class ?>" data-upsale='<?= json_encode($upsaleData) ?>'><?= $value ?></a>
    </div>

<? };