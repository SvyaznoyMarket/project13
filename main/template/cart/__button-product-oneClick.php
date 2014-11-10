<?php

return function (
    \Helper\TemplateHelper $helper,
    \Model\Product\Entity $product,
    $url = null,
    $class = null,
    $value = 'Купить быстро в 1 клик',
    \Model\Shop\Entity $shop = null
) {
    $region = \App::user()->getRegion();

    $isNewOneClick = false;
    try {
        $ordersNewTest = \App::abTest()->getTest('orders_new');
        $ordersNewSomeRegionsTest = \App::abTest()->getTest('orders_new_some_regions');
        if (true
            //($region && in_array($region->getId(), \App::config()->self_delivery['regions']))
            && \App::config()->newOrder
            && (
                (!in_array($region->getId(), [93746, 119623]) && $ordersNewTest && in_array($ordersNewTest->getChosenCase()->getKey(), ['new_1', 'new_2'], true)) // АБ-тест для остальных регионов
                || (in_array($region->getId(), [93746, 119623]) && $ordersNewSomeRegionsTest && in_array($ordersNewSomeRegionsTest->getChosenCase()->getKey(), ['new_1', 'new_2'], true)) // АБ-тест для Ярославля и Ростова-на-дону
            )
        ) {
            $isNewOneClick = true;
        }
    } catch (\Exception $e) {}

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
    if ($isNewOneClick && (false !== strpos($class, ' jsOneClickButton '))) {
        $class = str_replace(' jsOneClickButton ', ' jsOneClickButton-new ', $class);
    }
?>

    <div class="btnOneClickBuy">
        <a class="btnOneClickBuy__eLink <?= $class ?>" data-target="#jsOneClickContent" data-title="<?= $shop ? 'Резерв товара' : 'Купить быстро в 1 клик' ?>" data-shop="<?= $shop ? $shop->getId() : null ?>" href="<?= $url ?>"><?= $value ?></a>
    </div>

<? };