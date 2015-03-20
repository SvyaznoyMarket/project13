<?php

/**
 * @param \Helper\TemplateHelper $helper
 * @param \Model\Order\Entity[] $orders
 * @param \Model\Product\Entity[] $productsById
 * @return string
 */
$f = function(
    \Helper\TemplateHelper $helper,
    $orders,
    $productsById = []
) {
    /** @var \Model\Product\Entity $product */

    /** @var \Model\Order\Entity $order */
    $order = reset($orders);
    if (!$order) return '';

    $product = reset($productsById);

    $delivery = $order->getDelivery();
?>

    <div class="orderCol_cnt clearfix">
        <div class="orderCol_lk">
            <img class="orderCol_img" src="<?= $product->getImageUrl(1) ?>">
        </div>

        <? if ($product): ?>
            <div class="orderCol_n">
                <? if ($product->getPrefix()): ?>
                    <?= $product->getPrefix() ?><br/>
                <? endif ?>
                <?= $product->getWebName() ?>
            </div>
        <? endif ?>

        <span class="orderCol_data orderCol_data-price"><?= $helper->formatPrice($order->getProduct()[0]->getPrice()) ?> <span class="rubl">p</span></span>
    </div>

    <? if ($delivery): ?>
        <div class="orderCol_f clearfix">
            <div class="orderCol_f_r">
                <span class="orderCol_summ">
                    <? if ($delivery->getPrice()): ?>
                    <?= $helper->formatPrice($delivery->getPrice()) ?> <span class="rubl">p</span></span>
                <? else: ?>
                    Бесплатно
                <? endif ?>
                </span>
                <span class="orderCol_summt">
                    <? if (in_array($order->getDeliveryTypeId(), [1, 2])): ?>
                        Доставка:
                    <? else: ?>
                        Самовывоз:
                    <? endif ?>
                </span>

                <span class="orderCol_summ"><?= $helper->formatPrice($order->getSum()) ?> <span class="rubl">p</span></span>
                <span class="orderCol_summt">Итого:</span>
            </div>
        </div>
    <? endif ?>


    <? if (\App::config()->googleAnalytics['enabled']): ?>
        <?= $helper->render('order/__analyticsData', ['orders' => $orders, 'productsById' => $productsById]) ?>
    <? endif ?>

<? }; return $f;
