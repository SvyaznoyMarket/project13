<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\OrderDelivery\Entity $orderDelivery,
    $shopId = null
) {
    $hasDiscountField = 'new_with_hidden_discount' === \App::abTest()->getOneClickView();
?>

    <div id="js-order-content" class="orderCnt jsOrderV3PageDelivery">
        <? $i = 0; foreach ($orderDelivery->orders as $order): $i++;?>
            <? if ((bool)$order->validationErrors) : ?>
                <div class="jsOrderValidationErrors" data-value="<?= $helper->json($order->validationErrors) ?>"></div>
            <? endif ?>

            <!-- блок разбиения заказа -->
            <div class="orderRow clearfix jsOneClickOrderRow" data-block_name="<?= $order->block_name ?>">
                <!-- информация о доставке -->
                <div class="orderCol <?= (false === $hasDiscountField ? 'orderCol-r' : '') ?><? if ($shopId): ?> orderCol-single<? endif ?>">
                    <? if (!$shopId): ?>
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
                    <? endif ?>

                    <!-- дата доставки -->
                    <div class="orderCol_delivrIn date clearfix" style="padding-left: 0;">
                        <? if (!$shopId): ?>
                            <? if ($date = $order->delivery->date): ?>
                                <? if ($order->delivery->dateInterval || $order->delivery->dayRange): ?>
                                    <?
                                    if ($order->delivery->dateInterval) {
                                        $shownDate = sprintf('с %s по %s', (new \DateTime($order->delivery->dateInterval['from']))->format('d.m'), (new \DateTime($order->delivery->dateInterval['to']))->format('d.m'));
                                    } else if ($order->delivery->dayRange) {
                                        $shownDate =
                                            !empty($order->delivery->dayRange['name'])
                                            ? $order->delivery->dayRange['name']
                                            : sprintf('%s-%s %s', $order->delivery->dayRange['from'], $order->delivery->dayRange['to'], $helper->numberChoice($order->delivery->dayRange['to'], ['день', 'дня', 'дней']))
                                        ;
                                    }
                                    ?>
                                    <span class="orderCol__term" data-content="#id-order-changeDate-content-<?= $order->id ?>" data-date="<?= $date->format('Y-m-d') ?>"><?= $shownDate ?></span>
                                <? else: ?>
                                    <?
                                    $shownDate = mb_strtolower(\Util\Date::strftimeRu('%e %B2 %Y, %A', $order->delivery->date->format('U')));
                                    ?>
                                    <div class="orderCol_date" data-content="#id-order-changeDate-content-<?= $order->id ?>"><?= $shownDate ?></div>
                                <? endif ?>
                            <? endif ?>

                            <?= $helper->render('order-v3/__calendar', [
                                'id'            => 'id-order-changeDate-content-' . $order->id,
                                'possible_days' => $order->possible_days,
                                'position'      => 'bottom',
                            ]) ?>

                            <? if ((bool)$order->possible_intervals) : ?>
                                <?= $helper->render('order-v3/common/_delivery-interval', ['order' => $order]) ?>
                            <? endif ?>
                        <? endif ?>

                    </div>
                    <!--/ дата доставки -->
                    <!-- регион доставки -->
                    <? if ($hasDiscountField): ?>
                        <div class="order-region">Ваш регион: <span class="order-region__change jsChangeRegion"><?= \App::user()->getRegion()->getName() ?></span></div>
                    <? endif ?>
                    <!--END регион доставки -->
                    <!-- способ доставки -->
                    <? if (!$order->delivery->use_user_address): ?>
                        <? $point = $order->delivery->point ? $orderDelivery->points[$order->delivery->point->token]->list[$order->delivery->point->id] : null ?>

                        <div class="orderCol_delivrIn orderCol_delivrIn-pl">
                            <div class="orderCol_delivrIn_t clearfix">
                                <strong><?= $orderDelivery->delivery_groups[$orderDelivery->delivery_methods[$order->delivery->delivery_method_token]->group_id]->name ?></strong>

                                <? if (!$shopId): ?><span class="js-order-changePlace-link orderChange" data-content="#id-order-changePlace-content-<?= $order->id ?>" data-order-id="<?= $helper->escape($order->id) ?>">изменить место</span><? endif ?>
                            </div>

                            <div class="orderCol_addrs"<? if (isset($point->subway[0]->line)): ?> style="background: <?= $point->subway[0]->line->color ?>;"<? endif ?>>
                                <span class="orderCol_addrs_tx">
                                    <? if (isset($point->subway[0])): ?><?= $point->subway[0]->name ?><br/><? endif ?>
                                    <? if (isset($point->address)): ?><span class="colorBrightGrey"><?= $point->address ?></span><? endif ?>
                                </span>
                            </div>

                            <div class="orderCol_tm">
                                <? if (isset($point->regtime)): ?><span class="orderCol_tm_t">Режим работы:</span> <?= $point->regtime ?><? endif ?>
                                <? if (isset($point) && !$order->prepaid_sum) : ?>
                                    <br />
                                    <span class="orderCol_tm_t">Оплата при получении: </span>
                                    <? if (isset($order->possible_payment_methods[\Model\PaymentMethod\PaymentMethod\PaymentMethodEntity::PAYMENT_CASH])) : ?><!--<img class="orderCol_tm_img" src="/styles/order/img/cash.png" alt="">-->наличные<? endif ?><? if (isset($order->possible_payment_methods[\Model\PaymentMethod\PaymentMethod\PaymentMethodEntity::PAYMENT_CARD_ON_DELIVERY])) : ?><!--<img class="orderCol_tm_img" src="/styles/order/img/cards.png" alt="">-->, банковская карта<? endif ?>
                                <? endif ?>
                            </div>
                        </div>
                    <? else: ?>
                        <!--                    <div class="orderCol_delivrIn_t clearfix">
                                                <strong>Адрес</strong>
                                            </div>-->
                        <? if (!$hasDiscountField): ?>
                            <div class="orderCol_addrs" style="margin-left: 0;">
                                <?= $helper->render('order-v3/common/_smartaddress') ?>
                            </div>

                        <? endif ?>

                        <? if ($hasDiscountField): ?>
                        <div class="order-delivery__block deliv-addr jsSmartAddressBlock id-order-deliveryAddress-standart_svyaznoy">
                            <div class="order-ctrl fullwidth error">
                                <label class="order-ctrl__txt js-order-ctrl__txt">Улица</label>
                                <span role="status" aria-live="polite" class="ui-helper-hidden-accessible"></span><input type="text" value="" class="order-ctrl__input order-ctrl__input_float-label js-order-ctrl__input js-order-deliveryAddress ui-autocomplete-input" data-field="street" required="" data-value="{&quot;block_name&quot;:&quot;standart_svyaznoy&quot;}" data-relation="{&quot;container&quot;:&quot;.id-order-deliveryAddress-standart_svyaznoy&quot;}" data-text-default="Улица" data-parent-kladr-id="7700000000000" autocomplete="off">
                            </div>
                            <div class="order-ctrl">
                                <label class="order-ctrl__txt js-order-ctrl__txt">*Дом</label>
                                <input type="text" value="" class="order-ctrl__input order-ctrl__input_float-label js-order-ctrl__input js-order-deliveryAddress" data-field="building" required="" data-value="{&quot;block_name&quot;:&quot;standart_svyaznoy&quot;}" data-text-default="Дом" data-relation="{&quot;container&quot;:&quot;.id-order-deliveryAddress-standart_svyaznoy&quot;}">
                            </div>
                            <div class="order-ctrl">
                                <label class="order-ctrl__txt js-order-ctrl__txt">Квартира</label>
                                <input type="text" value="" class="order-ctrl__input order-ctrl__input_float-label js-order-ctrl__input js-order-deliveryAddress" data-field="apartment" data-value="{&quot;block_name&quot;:&quot;standart_svyaznoy&quot;}" data-relation="{&quot;container&quot;:&quot;.id-order-deliveryAddress-standart_svyaznoy&quot;}">
                            </div>
                        </div>
                        <? endif ?>
                    <? endif ?>

                    <?
                    $dataPoints = (new \View\PointsMap\MapView());
                    $dataPoints->preparePointsWithOrder($order, $orderDelivery);
                    ?>

                    <?= \App::templating()->render('order-v3/common/_map', [
                        'dataPoints' => $dataPoints,
                        'page'       => 'order',
                        'order'      => $order,
                    ]) ?>

                    <!--/ способ доставки -->
                </div>
                <!--/ информация о доставке -->
            <? if ($hasDiscountField): ?>
                <div class="order-discount">

                    <span class="order-discount__tl">Код скидки/фишки, подарочный сертификат</span>
                    <div class="order-discount__current">
                        <div class="order-discount__ep-img-block">
                            <span class="ep-coupon order-discount__ep-coupon-img" style="background-image: url(http://content.enter.ru/wp-content/uploads/2014/03/fishka_orange_b1.png);">
                                <span class="ep-coupon__ico order-discount__ep-coupon-icon">
                                    <img src="http://scms.enter.ru/uploads/media/e1/d7/a8/61389c42d60a432bd426ad08a0306fe0ca638ff7.png">
                                </span>
                            </span>
                        </div>
                        <div class="order-discount__current-txt">
                            Применена "Фишка со скидкой 10% на Новогодние украшения и подарки"
                        </div>
                    </div>
                    <div class="order-ctrl">
                        <input class="order-ctrl__input id-discountInput-standarttype3 <?= $inputSelectorId ?>" value="">
                        <label class="order-ctrl__lbl nohide"></label>
                    </div>

                    <button
                        class="order-btn order-btn--default jsApplyDiscount-1509"
                        data-relation="<?= $helper->json([
                            'number' => '.' . $inputSelectorId,
                        ]) ?>"
                    >Применить</button>

                    <div class="jsCertificatePinField order-discount__pin">
                        <label class="order-discount__pin-label">Пин код</label>
                        <input class="order-discount__pin-input order-ctrl__input jsCertificatePinInput" type="text" name="" value="">
                    </div>
                </div>


                <div  class="orderOneClick-new__fin-sum">
                    <span>Сумма заказа:</span>
                    <span class="orderOneClick-new__fin-sum-num"><?= $helper->formatPrice($orderDelivery->total_cost) ?> <span class="rubl">p</span></span>
                </div>
            </div>
            <? endif ?>
            <!--/ блок разбиения заказа -->
        <? endforeach ?>
    </div>

<? };