<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\OrderDelivery\Entity $orderDelivery,
    $error = null
) {
?>


<div id="js-order-content" class="orderCnt jsOrderV3PageDelivery">
    <? $i = 0; foreach ($orderDelivery->orders as $order): $i++;?>
    <? if ((bool)$order->validationErrors) : ?>
        <div class="jsOrderValidationErrors" data-value="<?= $helper->json($order->validationErrors) ?>"></div>
    <? endif ?>

    <!-- блок разбиения заказа -->
    <div class="orderRow clearfix" data-block_name="<?= $order->block_name ?>">
        <!-- информация о доставке -->
        <div class="orderCol orderCol-r">
            <menu class="orderCol_delivrLst clearfix">
            <? foreach ($order->possible_delivery_groups as $deliveryGroup): ?>
                <?  // Определение первого доступного delivery_method-а для группы
                    $delivery_methods_for_group = array_filter($order->possible_deliveries, function($delivery) use ($deliveryGroup) { return $delivery->group_id == $deliveryGroup->id; } );
                    $first_delivery_method = reset($delivery_methods_for_group);
                    $first_delivery_method_token = $first_delivery_method->token;
                    ?>
                <li class="orderCol_delivrLst_i <? if ($deliveryGroup->id == $order->delivery_group_id): ?>orderCol_delivrLst_i-act<? endif ?>"
                    data-delivery_group_id="<?= $deliveryGroup->id ?>"
                    data-delivery_method_token="<?= (string)$first_delivery_method_token ?>">
                    <span class="<? if ($deliveryGroup->id != $order->delivery_group_id): ?>orderCol_delivrLst_i_span_inactive<? endif ?>"><?= $deliveryGroup->name ?></span>
                </li>
            <? endforeach ?>
            </menu>

            <!-- дата доставки -->
            <div class="orderCol_delivrIn clearfix" style="padding-left: 0;">
                <!--<div class="orderCol_date">15 сентября 2014, воскресенье</div>-->
                <? if ($order->delivery->date): ?>
                    <div class="orderCol_date" data-content="#id-order-changeDate-content-<?= $order->id ?>"><?= mb_strtolower(\Util\Date::strftimeRu('%e %B2 %G, %A', $order->delivery->date->format('U'))) ?></div>
                <? endif ?>

                <?= $helper->render('order-v3/__calendar', [
                    'id'            => 'id-order-changeDate-content-' . $order->id,
                    'possible_days' => $order->possible_days,
                ]) ?>

                <? if ((bool)$order->possible_intervals) : ?>

                <div class="customSel">
                    <? if ($order->delivery->interval) : ?>
                        <span class="customSel_def"><?= $order->delivery->interval['from'] ?>…<?= $order->delivery->interval['to'] ?></span>
                    <? else : ?>
                        <span class="customSel_def">Время доставки</span>
                    <? endif ?>

                    <ul class="customSel_lst popupFl" style="display: none;">
                        <? foreach ($order->possible_intervals as $interval) : ?>
                        <li class="customSel_i" data-value="<?= $helper->json($interval) ?>"><?= isset ($interval['from']) ? $interval['from'] : '' ?>…<?= isset ($interval['to']) ? $interval['to'] : '' ?></li>
                        <? endforeach; ?>
                    </ul>
                </div>

                <? endif; ?>

            </div>
            <!--/ дата доставки -->

            <!-- способ доставки -->
            <? if (!$order->delivery->use_user_address): ?>
                <? $point = $order->delivery->point ? $orderDelivery->points[$order->delivery->point->token]->list[$order->delivery->point->id] : null ?>

                <div class="orderCol_delivrIn orderCol_delivrIn-pl">
                    <div class="orderCol_delivrIn_t clearfix">
                        <strong><?= $orderDelivery->delivery_groups[$orderDelivery->delivery_methods[$order->delivery->delivery_method_token]->group_id]->name ?></strong>

                        <span class="js-order-changePlace-link orderChange" data-content="#id-order-changePlace-content-<?= $order->id ?>">изменить место</span>
                    </div>

                    <div class="orderCol_addrs"<? if (isset($point->subway[0]->line)): ?> style="background: <?= $point->subway[0]->line->color ?>;"<? endif ?>>
                        <span class="orderCol_addrs_tx">
                            <? if (isset($point->subway[0])): ?><?= $point->subway[0]->name ?><br/><? endif ?>
                            <? if (isset($point->address)): ?><span class="colorBrightGrey"><?= $point->address ?></span><? endif; ?>
                        </span>
                    </div>

                    <div class="orderCol_tm">
                        <? if (isset($point->regtime)): ?><span class="orderCol_tm_t">Режим работы:</span> <?= $point->regtime ?><? endif ?>
                        <? if (isset($point)) : ?>
                            <br />
                            <span class="orderCol_tm_t">Оплата при получении: </span>
                            <? if (isset($order->possible_payment_methods[\Model\PaymentMethod\PaymentMethod\PaymentMethodEntity::PAYMENT_CASH])) : ?><!--<img class="orderCol_tm_img" src="/styles/order/img/cash.png" alt="">-->наличные<? endif; ?><? if (isset($order->possible_payment_methods[\Model\PaymentMethod\PaymentMethod\PaymentMethodEntity::PAYMENT_CARD_ON_DELIVERY])) : ?><!--<img class="orderCol_tm_img" src="/styles/order/img/cards.png" alt="">-->, банковская карта<? endif; ?>
                        <? endif; ?>
                    </div>
                </div>
            <? else: ?>
                <div class="orderCol_delivrIn orderCol_delivrIn-pl">
                    <div class="orderCol_delivrIn_t clearfix">
                        <strong>Адрес</strong> <span class="colorBrightGrey">для всех заказов с доставкой</span>
                    </div>

                    <div class="orderCol_addrs">
                        <ul class="orderCol_addrs_fld textfield clearfix" style="height: inherit">
                            <li class="orderCol_addrs_fld_i orderCol_addrs_fld_i-edit ui-front">
                                <span id="addressInputPrefix" class="addrsAutocmpltLbl"></span><input name="address" type="text" />
                            </li>
                        </ul>
                    </div>

                </div>
            <? endif ?>

            <?= $helper->render('order-v3/__map', [
                'id'            => 'id-order-changePlace-content-' . $order->id,
                'order'         => $order,
                'orderDelivery' => $orderDelivery
            ]) ?>

            <!--/ способ доставки -->
        </div>
        <!--/ информация о доставке -->
    </div>
    <!--/ блок разбиения заказа -->
    <? endforeach ?>
</div>

<? };