<?php

return function (
    \Helper\TemplateHelper $helper,
    \Model\Product\Entity $product,
    $url = null,
    $class = null,
    $value = 'Купить быстро в 1 клик',
    $shop = null
) {
    $region = \App::user()->getRegion();

    if (
        !$product->getIsBuyable()
        || (5 === $product->getStatusId()) // SITE-2924
    ) {
        return '';
    }

    if (!$product->getKit()) {
        $class = \View\Id::cartButtonForProduct($product->getId() . '-oneClick') . ' jsOneClickButton ' . $class;
    }

    if ($product->isInShopStockOnly()) {
        $class .= ' mShopsOnly';
    } elseif ($product->isInShopShowroomOnly()) {
        $class .= ' mShopsOnly';
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
        $url = $helper->url('cart.oneClick.product.set', $urlParams);
    }

    if ($product->getKit() && !$product->getIsKitLocked()) {
        $urlParams = [];
        foreach ($product->getKit() as $kitItem) {
            $urlParams['product'][] = ['id' => $kitItem->getId(), 'quantity' => $kitItem->getCount()];
        }
        $url = $helper->url('cart.oneClick.product.setList', $urlParams);
    }

    // FIXME: резерв по старому
    if ((false !== strpos($class, ' jsOneClickButton ')) && !$shop) {
        $class = str_replace(' jsOneClickButton ', ' jsOneClickButton-new ', $class);
    }
?>

    <div class="btnOneClickBuy">
        <a class="btnOneClickBuy__eLink <?= $class ?>" data-target="#jsOneClickContent" href="<?= $url ?>"><?= $value ?></a>
    </div>

    <? if (!$shop): ?>
        <div id="yandex-map-container" class="selShop_r" style="display: none;" data-options="<?= $helper->json(['latitude' => $region->getLatitude(), 'longitude' => $region->getLongitude(), 'zoom' => 10])?>"></div>
        <div id="kladr-config" data-value="<?= $helper->json(\App::config()->kladr ); ?>"></div>
        <div id="region-name" data-value=<?= json_encode($region->getName(), JSON_UNESCAPED_UNICODE); ?>></div>

        <div id="jsOneClickContent" class="popup popup-w635">
            <a class="close" href="#">Закрыть</a>

            <div id="jsOneClickContentPage">
                <?= $helper->render('order-v3-1click/__form', [
                    'product' => $product,
                    'shop'    => $shop,
                ]) ?>
            </div>
        </div>
    <? endif ?>

<? };