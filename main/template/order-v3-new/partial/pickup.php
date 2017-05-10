<?php
return function(
    \Helper\TemplateHelper $helper,
    \Model\OrderDelivery\Entity\Order $order,
    \Model\OrderDelivery\Entity $orderDelivery
) {
    $point = $order->delivery->point ? $orderDelivery->points[$order->delivery->point->token]->list[$order->delivery->point->id] : null;

    $availablePaymentMethods = [];
    if ($point && !$order->prepaid_sum) {
        if (isset($order->possible_payment_methods[\Model\PaymentMethod\PaymentMethod\PaymentMethodEntity::PAYMENT_CASH])) {
            $availablePaymentMethods[] = 'наличные';
        }

        if (isset($order->possible_payment_methods[\Model\PaymentMethod\PaymentMethod\PaymentMethodEntity::PAYMENT_CARD_ON_DELIVERY])) {
            $availablePaymentMethods[] = 'банковская карта';
        }
    }
?>

    <div class="order-delivery__block <?= ($order->delivery->point && $order->delivery->point->isSvyaznoy()) ? 'warn' : ''  ?>">
        <? if ($order->delivery->point): ?>
            <div class="order-delivery__block-inner">
                <a class="order-delivery__del js-order-deletePlace" href="#"></a>
                <div class="order-delivery__shop">
                    <? if (in_array($order->delivery->delivery_method->token, ['self', 'now'], true)): ?>
                        Магазин Enter
                    <? elseif ($order->delivery->delivery_method->token === 'self_svyaznoy'): ?>
                        Магазин Связной
                    <? else: ?>
                        <?= strtr(@$order->delivery->delivery_method->name, ['Hermes DPD' => 'Hermes']) ?>
                    <? endif ?>
                </div>

                <div class="order__addr">
                    <div class="order__point">
                        <div class="order__point-addr" <? if (isset($point->subway[0]->line)): ?> style="background: <?= $point->subway[0]->line->color ?>;"<? endif ?>>
                        <span class="order__addr-tx">
                            <? if (isset($point->subway[0])): ?><?= $point->subway[0]->name ?><br/><? endif ?>
                            <? if (isset($point->address)): ?><?= $point->address ?><? endif ?>
                        </span>
                        </div>
                    </div>
                </div>

                <div class="order-delivery__point-info">
                    <? if (!empty($point->regtime)): ?>
                        <p>Режим работы: <?= $point->regtime ?></p>
                    <? endif ?>

                    <? if ($availablePaymentMethods): ?>
                        <p>Оплата при получении: <?= $helper->escape(implode(', ', $availablePaymentMethods)) ?></p>
                    <? endif ?>
                </div>

                <? /*if ($order->delivery->point->isSvyaznoy()) : ?>
                    <span class="order-warning">В магазинах «Связной» не принимаются бонусы «Спасибо от Сбербанка»</span>
                <? endif*/ ?>
            </div>
        <? endif ?>

        <span class="js-order-changePlace-link order-delivery__change-place" data-content="#id-order-changePlace-content-<?= $order->id ?>">
            <?= ($order->delivery->point) ? 'Изменить пункт выдачи заказов' : 'Указать пункт выдачи заказов' ?>
        </span>

        <?= $helper->render('order-v3-new/partial/delivery-interval', ['order' => $order]) ?>
    </div>

<? };
