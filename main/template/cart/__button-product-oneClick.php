<?php

return function (
    \Helper\TemplateHelper $helper,
    \Model\Product\Entity $product,
    $url = null,
    $class = null,
    $value = 'Купить быстро в 1 клик',
    \Model\Shop\Entity $shop = null,
    $sender = [],
    $sender2 = '',
    $location = ''
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
                (!in_array($region->getId(), [93746, 119623]) && $ordersNewTest && in_array($ordersNewTest->getChosenCase()->getKey(), ['new_1', 'new_2', 'default'], true)) // АБ-тест для остальных регионов
                || (in_array($region->getId(), [93746, 119623]) && $ordersNewSomeRegionsTest && in_array($ordersNewSomeRegionsTest->getChosenCase()->getKey(), ['new_1', 'new_2', 'default'], true)) // АБ-тест для Ярославля и Ростова-на-дону
            )
        ) {
            $isNewOneClick = true;
        }
    } catch (\Exception $e) {}

    if (
        !$product->getIsBuyable()
        || (5 === $product->getStatusId()) // SITE-2924
        || (\App::abTest()->isOrderMinSumRestriction() && $product->getPrice() < \App::config()->minOrderSum)
    ) {
        return '';
    }

    if (!$product->getKit()) {
        $class = \View\Id::cartButtonForProduct($product->getId() . '-oneClick') . ' jsOneClickButton ' . $class;
    }

    /*
    if ($product->isInShopStockOnly()) {
        $class .= ' mShopsOnly';
    } elseif ($product->isInShopShowroomOnly()) {
        $class .= ' mShopsOnly';
    }
    */

    if ($product->getIsBuyable() && $shop) {
        $class .= \Session\AbTest\AbTest::getColorClass($product);
    }

    if (!$product->getIsBuyable()) {
        $url = '#';
        $class .= ' mDisabled';
        $value = $product->isInShopShowroomOnly() ? 'На витрине' : 'Нет в наличии';
    } else if (!isset($url)) {
        $urlParams = [
            'productId' => $product->getId(),
        ];

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

        $url = $helper->url('cart.oneClick.product.set', $urlParams);
    }

    if ($product->getKit() && !$product->getIsKitLocked()) {
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

        $url = $helper->url('cart.oneClick.product.setList', $urlParams);
    }

    if ($isNewOneClick && (false !== strpos($class, ' jsOneClickButton '))) {
        $class = str_replace(' jsOneClickButton ', ' jsOneClickButton-new ', $class);
    }

    $id = 'quickBuyButton-' . $product->getId();
?>

    <!--noindex-->
    <div class="btnOneClickBuy">
        <a
            href="<?= $url ?>"
            id="<?= $id ?>"
            class="btnOneClickBuy__eLink <?= $class ?>"
            data-shop="<?= $shop ? $shop->getId() : null ?>"
            data-product-ui="<?= $helper->escape($product->getUi()) ?>"
            data-sender="<?= $helper->json($sender) ?>"
            data-sender2="<?= $helper->escape($sender2) ?>"
            <? if ($location): ?>
                data-location="<?= $helper->escape($location) ?>"
            <? endif ?>
        ><?= $value ?></a>
    </div>
    <!--/noindex-->

<? };