<?php

return function (
    \Helper\TemplateHelper $helper,
    \Model\Product\Entity $product,
    $url = null,
    $class = null,
    $value = 'Купить быстро в 1 клик'
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

?>

    <div class="btnOneClickBuy">
        <a class="btnOneClickBuy__eLink <?= $class ?>" data-target="#jsOneClickContent" href="<?= $url ?>"><?= $value ?></a>
    </div>

    <div id="jsOneClickContent" class="popup">
        <a class="close" href="#">Закрыть</a>

        <?= $helper->render('order-v3-1click/__form', [
            'user'          => \App::user(),
            'orderDelivery' => $helper->getParam('orderDelivery'),
        ]) ?>
    </div>

    <div id="yandex-map-container" class="selShop_r" style="display: none;" data-options="<?= $helper->json(['latitude' => $region->getLatitude(), 'longitude' => $region->getLongitude(), 'zoom' => 10])?>"></div>
    <div id="kladr-config" data-value="<?= $helper->json(\App::config()->kladr ); ?>"></div>

<? };