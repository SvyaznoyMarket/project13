<?php

return function (
    \Helper\TemplateHelper $helper,
    \Model\Product\BasicEntity $product,
    array $sender = [],
    $sender2 = ''
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

    if ($product->getKit()) {
        $urlParams = [];
        foreach ($product->getKit() as $kitItem) {
            $urlParams['product'][] = ['id' => $kitItem->getId(), 'quantity' => $kitItem->getCount()];
        }

        if ($sender) {
            $urlParams = array_merge($urlParams, [
                'sender' => [
                    'name'      => isset($sender['name']) ? $sender['name'] : null,
                    'position'  => isset($sender['position']) ? $sender['position'] : null,
                    'method'    => isset($sender['method']) ? $sender['method'] : null,
                    'from'      => isset($sender['from']) ? $sender['from'] : null,
                ],
            ]);
        }

        if ($sender2) {
            $urlParams['sender2'] = $sender2;
        }

        $url = $helper->url('cart.product.setList', $urlParams);
    }

    if (5 === $product->getStatusId()) { // SITE-2924
        return '';
    } else if (!$product->getIsBuyable()) {
        $url = null;
        $class .= ' mDisabled';
        $class = str_replace(' jsChangePackageSet', '', $class);
        $value = $product->isInShopShowroomOnly() ? 'На витрине' : 'Нет';
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
    <div class="bWidgetBuy__eBuy btnBuy mBuySet">
        <? if ($url === null): ?>
            <span class="<?= $class ?>"><?= $value ?></span>
        <? else: ?>
            <a href="<?= $url ?>" class="<?= $class ?>" data-upsale='<?= json_encode($upsaleData) ?>'><?= $value ?></a>
        <? endif ?>
    </div>

<? };