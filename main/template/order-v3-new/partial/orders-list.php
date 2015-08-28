<?php

use \Model\PaymentMethod\PaymentMethod\PaymentMethodEntity as PaymentMethod;

return function (
    \Helper\TemplateHelper $helper,
    \Model\OrderDelivery\Entity $orderDelivery,
    $error = null
) {
    $orderCount = count($orderDelivery->orders);
    $region = \App::user()->getRegion();
    $firstOrder = reset($orderDelivery->orders);
    $i = 0;

    $isCoordsValid = $region && $region->getLatitude() != null && $region->getLongitude() != null;

    $initialMapCords = [
        'latitude' => $isCoordsValid ? $region->getLatitude() : 55.76,
        'longitude' => $isCoordsValid ? $region->getLongitude() : 37.64,
        'zoom' => $isCoordsValid ? 10 : 4
    ];

    ?>

    <? foreach ($orderDelivery->orders as $order): $i++; ?>
        <? if ((bool)$order->validationErrors) : ?>
            <div class="jsOrderValidationErrors" data-value="<?= $helper->json($order->validationErrors) ?>"></div>
        <? endif; ?>

        <!-- блок разбиения заказа -->
        <div class="order-bill__item clearfix jsOrderRow <?= $order->isPartnerOffer() ? 'jsPartnerOrder' : '' ?>"
             data-block_name="<?= $order->block_name ?>">
            <!-- информация о заказе -->
            <div class="order-bill__head">Заказ №<?= ($i) ?></div>
            <!-- левая часть блока заказа - список заказанных товаров-->
            <div class="order-bill__goods">
                <? if ($order->seller): ?>
                    <div class="order-bill__seller">продавец: <?= $order->seller->name ?> <a
                            class="order-bill__oferta js-order-oferta-popup-btn" href="<?= $order->seller->offer ?>"
                            data-value="<?= $order->seller->offer ?>" target="_blank">Информация и оферта</a></div>
                <? endif ?>
                <? /*if (!\App::config()->order['prepayment']['priceLimit'] || ($order->total_cost > \App::config()->order['prepayment']['priceLimit'])) : ?>
                    <div class="orderCol orderCol_warn"><span class="orderCol_warn_l">Требуется предоплата.</span> <span
                            class="orderCol_warn_r">Сумма заказа превышает 100&nbsp;000&nbsp;руб. <a href="/how_pay"
                                                                                                     target="_blank">Подробнее</a></span>
                    </div>
                <? endif; */?>

                <? foreach ($order->products as $product): ?>
                    <div class="order-good__item">
                        <a href="<?= $product->link ?>" class="order-good__item-lk" target="_blank">
                            <img class="order-good__img" src="<?= $product->getMainImageUrl('product_60') ?>"
                                 alt="<?= $helper->escape($product->name) ?>"/>
                        </a>

                        <a class="order-good__name" href="<?= $product->link ?>" target="_blank">
                            <? if ($product->prefix): ?><?= $product->prefix ?><br/><? endif ?>
                            <?= $product->name_web ?>
                        </a>

                        <span class="order-good__price"><?= $helper->formatPrice($product->original_sum) ?>
                            <span class="rubl">p</span></span>
                        <span class="order-good__quantity"><?= $product->quantity ?> шт.</span>

                        <!-- редактирование кол-ва/удаление товара -->
                        <div class="order-good__edit" style="display: none">
                            <div data-spinner-for="" class="order-good__count count">
                                <button class="count__ctrl count__ctrl--less" title="Уменьшить">−</button>
                                <input name="productQuantity[]" type="text" value="<?= $product->quantity ?>"
                                       class="count__num" data-stock="<?= $product->stock ?>"/>
                                <button class="count__ctrl count__ctrl--more" title="Увеличить">+</button>
                            </div>
                            <span class="order-good__units">шт.</span>

                            <a class="order-good__apply jsChangeProductQuantity" href="" data-id="<?= $product->id; ?>"
                               data-ui="<?= $product->ui; ?>" data-block_name="<?= $order->block_name ?>">Применить</a>
                            &nbsp;|&nbsp;
                            <a class="order-good__del jsDeleteProduct" href="" data-id="<?= $product->id; ?>"
                               data-ui="<?= $product->ui; ?>" data-block_name="<?= $order->block_name ?>">Удалить
                                товар</a>
                        </div>
                        <!-- END редактирование кол-ва/удаление товара -->

                        <span class="order-good__total-price"><?= $helper->formatPrice($product->original_price) ?>
                            <span class="rubl">p</span></span>
                        <?= $helper->render('order-v3-new/__errors', ['orderDelivery' => $orderDelivery, 'order' => $order, 'product' => $product]) ?>
                    </div>
                <? endforeach ?>

                <!-- введенные скидки, купоны -->

                <? if ((bool)$order->discounts || $order->certificate['par'] !== null) : ?>

                    <div class="order-discount__head">Скидки</div>

                    <? foreach ($order->discounts as $discount) : ?>

                        <div class="order-discount__item jsOrderV3Discount">
                            <div class="order-discount__cell">
                                <?// if ($discount->number !== null) : ?> <!-- это условие точно нужно? Как по мне - если уж вывели фишку, надо дать возможность ее удалить-->
                                    <span
                                        class="order-discount__del jsDeleteDiscount"
                                        data-value="<?= $discount->number ?>">удалить</span>
                                <?// endif ?>
                            </div>
                            <a href="" class="order-discount__lk">
                                <img class="order-discount__img" src="/styles/order-new/img/chip.png" alt="">
                            </a>

                            <div class="order-discount__name">
                                Фишка на скидку<?= $discount->name; ?>
                            </div>

                            <div
                                class="order-discount__val">-<?= $discount->discount ?>500
                                <span class="rubl">p</span></div>

                        </div>

                    <? endforeach; ?>

                    <? if ($order->certificate['par'] !== null) : ?>

                        <div class="order-discount__item clearfix">
                            <div class="order-discount__cell">
                                <span class="order-discount__del jsDeleteCertificate">удалить</span>
                            </div>
                            <a href="" class="order-discount__lk">
                                <img class="order-discount__img" src="/styles/order/img/enter.png" alt="">
                            </a>

                            <div class="order-discount__name">Подарочный сертификат <?= $order->certificate['par'] ?> <span class="rubl">p</span></div>

                            <span
                                class="order-discount__val">-<?= $order->certificate['par'] ?>
                                <span class="rubl">p</span></span>
                        </div>

                    <? endif; ?>

                <? endif; ?>
                <!-- END: введенные скидки, купоны -->

            </div>
            <!-- END левая часть блока заказа - список заказанных товаров-->


            <!-- правая часть блока заказа - выбор доставки/самовывоза -->
            <div class="order-bill__delivery-details">


                <!-- информация о доставке TODO: вынести блок в отдельный шаблон-->
                    <menu class="order-delivery__menu">
                        <? foreach ($order->possible_delivery_groups as $deliveryGroup): ?>
                            <? // Определение первого доступного delivery_method-а для группы
                            $delivery_methods_for_group = array_filter($order->possible_deliveries, function ($delivery) use ($deliveryGroup) {
                                return $delivery->group_id == $deliveryGroup->id;
                            });
                            $first_delivery_method = reset($delivery_methods_for_group);
                            $first_delivery_method_token = $first_delivery_method->token;
                            ?>
                            <li class="order-delivery__type jsDeliveryChange <? if ($deliveryGroup->id == $order->delivery_group_id): ?>active<? endif ?>"
                                data-delivery_group_id="<?= $deliveryGroup->id ?>"
                                data-delivery_method_token="<?= (string)$first_delivery_method_token ?>">
                            <span
                                class="order-delivery__type-inn<? if ($deliveryGroup->id != $order->delivery_group_id): ?> <? endif ?>"><?= $deliveryGroup->name ?></span><!-- скорее всего доп класс тут и не нужен, хватит того, что на li -->
                            </li>
                        <? endforeach ?>
                    </menu>
                    <!-- регион доставки -->
                    <div class="order-region">Ваш регион: <span class="order-region__change jsChangeRegion"><?= \App::user()->getRegion()->getName() ?></span></div>
                    <!--END регион доставки -->


                <!-- изменить/выбрать место - если у нас самовывоз-->
                <? if (!$order->delivery->use_user_address) {?>
                    <span class="js-order-changePlace-link order-delivery__change-place"
                          data-content="#id-order-changePlace-content-<?= $order->id ?>">
                                        <?= (!$order->delivery->point) ? 'Указать место самовывоза' : 'Изменить место самовывоза' ?>
                            </span>
                <? } ?>
                <!-- -->
                    <!-- дата доставки -->
                    <div class="order-delivery__info">
                        <!--<div class="orderCol_date">15 сентября 2014, воскресенье</div>-->
                        <? if ($order->delivery->date): ?>
                            <div class="order-delivery__date orderCol_date"
                                 data-content="#id-order-changeDate-content-<?= $order->id ?>"><?= mb_strtolower(\Util\Date::strftimeRu('%e %B2 %Y', $order->delivery->date->format('U'))) ?></div>
                        <? endif ?>

                        <?= $helper->render('order-v3-new/__calendar', [
                            'id' => 'id-order-changeDate-content-' . $order->id,
                            'possible_days' => $order->possible_days,
                        ]) ?>

                        <? if ((bool)$order->possible_intervals) : ?>
                            <?= $helper->render('order-v3-new/partial/delivery-interval', ['order' => $order]) ?>
                        <? endif; ?>

                    </div>
                    <!--/ дата доставки -->

                    <!-- способ доставки -->
                    <? if (!$order->delivery->use_user_address): ?>
                        <? $point = $order->delivery->point ? $orderDelivery->points[$order->delivery->point->token]->list[$order->delivery->point->id] : null ?>

                        <!--Добавляем класс warn если у нас будет текст-предупреждение о баллах связного: -->
                        <div
                            class="order-delivery__block <?= ($order->delivery->point && $order->delivery->point->isSvyaznoy()) ? 'warn' : ''  ?> <?= $order->delivery->point ? 'plain' : 'empty' ?>">

                            <? if ($order->delivery->point) { ?>
                                <div class="order-delivery__shop clearfix">
                                    <?= @$order->delivery->delivery_method->name ?>
                                </div>
                            <? }; ?>
                            <div class="order__addr">
                                <div class="order__point">
                                    <div class="order__point-addr" <? if (isset($point->subway[0]->line)): ?> style="background: <?= $point->subway[0]->line->color ?>;"<? endif ?>>
                                        <span class="order__addr-tx">
                                            <? if (isset($point->subway[0])): ?><?= $point->subway[0]->name ?><br/><? endif ?>
                                            <? if (isset($point->address)): ?><?= $point->address ?><? endif; ?>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="order-delivery__point-info">
                                <? if (isset($point->regtime)): ?>Режим работы: <?= $point->regtime ?><? endif ?>
                            </div>
                                <? if (isset($point) && (!\App::config()->order['prepayment']['priceLimit'] || ($order->total_cost < \App::config()->order['prepayment']['priceLimit']))) : ?>
                                    <br/>
                                    Оплата при получении:
                                    <? if (isset($order->possible_payment_methods[PaymentMethod::PAYMENT_CASH])) : ?>
                                        <!--<img class="orderCol_tm_img" src="/styles/order/img/cash.png" alt="">-->наличные
                                    <? endif; ?>
                                    <? if (isset($order->possible_payment_methods[PaymentMethod::PAYMENT_CARD_ON_DELIVERY])) : ?>
                                        <!--<img class="orderCol_tm_img" src="/styles/order/img/cards.png" alt="">-->, банковская карта
                                    <? endif; ?>
                                <? endif; ?>

                            <? if ($order->delivery->point && $order->delivery->point->isSvyaznoy()) : ?>
                                <span class="order-warning">В магазинах «Связной» не принимаются бонусы «Спасибо от Сбербанка»</span>
                            <? endif ?>
                        </div>

                        <!-- TODO: разместить вывод в блок <div class="order-delivery__block"> -->
                        <?= \App::abTest()->isOnlineMotivation(count($orderDelivery->orders)) ? $helper->render('order-v3-new/__payment-methods', ['order' => $order]) : '' ?>

                    <? else: ?>
                        <div class="order-delivery__block jsSmartAddressBlock">

                                <?= $helper->render('order-v3-new/partial/user-address') ?>

                        </div>


                            <?= \App::abTest()->isOnlineMotivation(count($orderDelivery->orders)) ? $helper->render('order-v3-new/__payment-methods', ['order' => $order]) : '' ?>

                            <? if (isset($order->possible_payment_methods[PaymentMethod::PAYMENT_CARD_ON_DELIVERY]) && !\App::abTest()->isOnlineMotivation(count($orderDelivery->orders))) : ?>

                                <div class="order-delivery__block">
                                    <? $checked = $order->payment_method_id == PaymentMethod::PAYMENT_CARD_ON_DELIVERY; ?>
                                    <input type="checkbox"
                                           class="customInput customInput-checkbox jsCreditCardPayment js-customInput"
                                           id="creditCardsPay-<?= $order->block_name ?>" name=""
                                           value="" <?= $checked ? 'checked ' : '' ?>/>
                                    <label class="customLabel customLabel-checkbox <?= $checked ? 'mChecked ' : '' ?>"
                                           for="creditCardsPay-<?= $order->block_name ?>">
                                    <span class="brb-dt"
                                          style="vertical-align: top;">Оплата курьеру банковской картой</span> <img
                                            class="orderCheck_img" src="/styles/order/img/i-visa.png" alt=""><img
                                            class="orderCheck_img" src="/styles/order/img/i-mc.png" alt="">
                                    </label>
                                </div>

                            <? endif; ?>

                    <? endif ?>

                    <?
                    $dataPoints = (new \View\PointsMap\MapView());
                    $dataPoints->preparePointsWithOrder($order, $orderDelivery);
                    ?>

                    <?= \App::templating()->render('order-v3/common/_map', [
                        'dataPoints' => $dataPoints,
                        'page' => 'order'
                    ]) ?>

                    <!--/ способ доставки -->
                    <? if (isset($order->possible_payment_methods[PaymentMethod::PAYMENT_CREDIT]) && !\App::abTest()->isOnlineMotivation(count($orderDelivery->orders))) : ?>

                        <div class="orderCheck orderCheck-credit clearfix">
                            <? $checked = $order->payment_method_id == PaymentMethod::PAYMENT_CREDIT; ?>
                            <input type="checkbox" class="customInput customInput-checkbox jsCreditPayment js-customInput"
                                   id="credit-<?= $order->block_name ?>" name="" value="" <?= $checked ? 'checked' : '' ?>>
                            <label class="customLabel customLabel-checkbox <?= $checked ? 'mChecked' : '' ?>"
                                   for="credit-<?= $order->block_name ?>"><span class="brb-dt">Купить в кредит</span><!--, от 2 223 <span class="rubl">p</span> в месяц-->
                            </label>
                        </div>

                    <? endif; ?>
                <!--/ информация о доставке -->

            </div>
            <!-- END правая часть блока заказа - выбор доставки/самовывоза -->



            <!-- ввести код скидки -->
            <div class="order-bill__adds">

                <div class="order-bill__total">
                        <span
                            class="order-bill__total-price"><?= $order->delivery->price == 0 ? 'Бесплатно' : $helper->formatPrice($order->delivery->price) . ' <span class="rubl">p</span>' ?></span>
                        <span
                            class="order-bill__serv"><?= $order->delivery->use_user_address ? 'Доставка' : 'Самовывоз' ?>
                            :</span>

                        <span class="order-bill__total-price"><?= $helper->formatPrice($order->total_cost) ?> <span
                                class="rubl">p</span></span>
                    <span class="order-bill__serv">Итого:</span>
                </div>

                <?= $helper->render('order-v3-new/partial/discount', ['order' => $order]) ?>

            </div>
            <!-- END ввести код скидки -->



            <!--/ информация о заказе -->


        </div>
        <!--/ блок разбиения заказа -->
    <? endforeach ?>

<? };