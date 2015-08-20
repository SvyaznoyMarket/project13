<?php

use \Model\PaymentMethod\PaymentMethod\PaymentMethodEntity as PaymentMethod;

return function(
    \Helper\TemplateHelper $helper,
    \Model\OrderDelivery\Entity $orderDelivery,
    $error = null
) {
    $orderCount = count($orderDelivery->orders);
    $region = \App::user()->getRegion();
    $firstOrder = reset($orderDelivery->orders);
    $i = 0;
    $isAjaxRequest = \App::request()->isXmlHttpRequest();

    $isCoordsValid = $region && $region->getLatitude() != null && $region->getLongitude() != null;

    $initialMapCords = [
        'latitude' => $isCoordsValid ? $region->getLatitude() : 55.76,
        'longitude' => $isCoordsValid ? $region->getLongitude() : 37.64,
        'zoom' => $isCoordsValid ? 10 : 4
    ];

    ?>

    <? if (!$isAjaxRequest) : ?>

        <section id="js-order-content" class="checkout jsOrderV3PageDelivery js-module-require" data-module="enter.order.step2">

    <? endif ?>

            <h1 class="checkout__title">Самовывоз и доставка</h1>

            <div class="checkout-order-location">
                Ваш регион: <span class="checkout-order-location__city"><?= \App::user()->getRegion()->getName() ?></span> <a href="" class="checkout-order-location__change dotted js-change-region jsRegionSelection">Изменить</a> <br/>
                От региона зависят доступные способы получения и оплаты заказов.
            </div>



        <? if ($orderCount != 1) : ?>
            <p class="checkout-order-split-info">Товары будут оформлены как <?= $orderCount ?> <?= $helper->numberChoice($orderCount, ['отдельный заказ', 'отдельных заказа', 'отдельных заказов']) ?></p>
        <? endif; ?>

        <?= $helper->render('order/_error.main', ['error' => $error, 'orderDelivery' => $orderDelivery]) ?>

        <? foreach ($orderDelivery->orders as $order): $i++;?>
            <? if ((bool)$order->validationErrors) : ?>
                <div class="jsOrderValidationErrors" data-value="<?= $helper->json($order->validationErrors) ?>"></div>
            <? endif; ?>
            <!-- блок разбиения заказа -->
            <div class="checkout-order js-order-block <?= $order->isPartnerOffer() ? 'jsPartnerOrder' : ''?>" data-block_name="<?= $order->block_name ?>">
                <!-- информация о доставке -->
                <div class="checkout-order__right">
                    <menu class="orderCol_delivrLst order-head">
                        <? foreach ($order->possible_delivery_groups as $deliveryGroup): ?>
                            <?  // Определение первого доступного delivery_method-а для группы
                            $delivery_methods_for_group = array_filter($order->possible_deliveries, function($delivery) use ($deliveryGroup) { return $delivery->group_id == $deliveryGroup->id; } );
                            $first_delivery_method = reset($delivery_methods_for_group);
                            $first_delivery_method_token = $first_delivery_method->token;
                            ?>
                            <li class="orderCol_delivrLst_i js-order-change-delivery-method <? if ($deliveryGroup->id == $order->delivery_group_id): ?>orderCol_delivrLst_i-act<? endif ?>"
                                data-delivery_group_id="<?= $deliveryGroup->id ?>"
                                data-delivery_method_token="<?= (string)$first_delivery_method_token ?>">
                                <span class="<? if ($deliveryGroup->id != $order->delivery_group_id): ?>orderCol_delivrLst_i_span_inactive<? endif ?>"><?= $deliveryGroup->name ?></span>
                            </li>
                        <? endforeach ?>
                    </menu>

                    <!-- дата доставки -->
                    <div class="checkout-order__content">
                        <!--<div class="order-delivery-info-date">15 сентября 2014, воскресенье</div>-->
                        <? if ($order->delivery->date): ?>
                            <div class="order-delivery-info-date js-order-open-calendar" data-content="#id-order-changeDate-content-<?= $order->id ?>"><?= mb_strtolower(\Util\Date::strftimeRu('%e %B2 %G, %A', $order->delivery->date->format('U'))) ?></div>
                        <? endif ?>

                        <?= $helper->render('order/_calendar', [
                            'id'                => 'id-order-changeDate-content-' . $order->id,
                            'possible_days'     => $order->possible_days,
                            'choosenDay'        => $order->delivery->date
                        ]) ?>

                        <? if ((bool)$order->possible_intervals) : ?>
                            <?= $helper->render('order/common/delivery.interval', ['order' => $order]) ?>
                        <? endif; ?>
                        <!--/ дата доставки -->

                        <!-- способ доставки -->
                        <? if (!$order->delivery->use_user_address): ?>
                            <? $point = $order->delivery->point ? $orderDelivery->points[$order->delivery->point->token]->list[$order->delivery->point->id] : null ?>
                            <!--Добавляем класс order-delivery-info-warn если у нас будет текст-предупреждение: -->
                            <div class="order-delivery-info <?= $order->delivery->point ? 'order-delivery-info_point' : 'order-delivery-info_empty' ?>">

                                <? if (!$order->delivery->point) : ?>
                                    <span class="js-order-changePlace-link dotted" style="cursor: pointer;" data-content="#id-order-changePlace-content-<?= $order->id ?>">Указать место самовывоза</span>
                                <? else : ?>
                                    <div class="order-delivery-info-title">
                                        <span class="order-delivery-info-title__left"><?= @$order->delivery->delivery_method->name ?></span>
                                        <span class="order-delivery-info-title__right dotted js-order-changePlace-link" data-content="#id-order-changePlace-content-<?= $order->id ?>">изменить место</span>
                                    </div>
                                <? endif; ?>

                                <div class="order-delivery-info-address"<? if (isset($point->subway[0]->line)): ?> style="background: <?= $point->subway[0]->line->color ?>;"<? endif ?>>
                                <span class="order-delivery-info-address__text">
                                    <? if (isset($point->subway[0])): ?><?= $point->subway[0]->name ?><? endif ?>
                                    <? if (isset($point->address)): ?><div class="order-delivery-info-address__text-mark"><?= $point->address ?></div><? endif; ?>
                                </span>
                                </div>

                                <div class="order-delivery-info-time">
                                    <? if (isset($point->regtime)): ?><span class="order-delivery-info-time__text">Режим работы:</span> <?= $point->regtime ?><? endif ?>
                                    <? if (isset($point)) : ?>
                                        <br />
                                        <span class="order-delivery-info-time__text">Оплата при получении: </span>
                                        <? if (isset($order->possible_payment_methods[PaymentMethod::PAYMENT_CASH])) : ?>
                                            <!--<img class="orderCol_tm_img" src="/styles/order/img/cash.png" alt="">-->наличные
                                        <? endif; ?>
                                        <? if (isset($order->possible_payment_methods[PaymentMethod::PAYMENT_CARD_ON_DELIVERY])) : ?>
                                            <!--<img class="orderCol_tm_img" src="/styles/order/img/cards.png" alt="">-->, банковская карта
                                        <? endif; ?>
                                    <? endif; ?>
                                </div>
                                <? if ($order->delivery->point && $order->delivery->point->isSvyaznoy()) : ?>
                                    <span class="order-warning">В магазинах «Связной» не принимаются бонусы «Спасибо от Сбербанка»</span>
                                <? endif ?>
                            </div>

                            <?= \App::abTest()->isOnlineMotivation(count($orderDelivery->orders)) ? $helper->render('order/_payment.methods', ['order' => $order]) : '' ?>

                        <? else: ?>
                            <div class="order-delivery-info order-delivery-info_transparent jsSmartAddressBlock">
                                <div class="order-delivery-info-title">
                                    <span class="order-delivery-info-title__left">Адрес</span>
                                </div>

                                <div class="form-address form">
                                    <div class="form-address__field form-address__field_street form__field">
                                        <input id="" type="text" class="form__it it" data-required="true" name="" value="">
                                        <label for="" class="form__placeholder placeholder placeholder_str">Улица</label>
                                    </div>

                                    <div class="form-address__field form-address__field_detail form__field">
                                        <input id="" type="text" class="form__it it" data-required="true" name="" value="">
                                        <label for="" class="form__placeholder placeholder placeholder_str">Дом</label>
                                    </div>

                                    <div class="form-address__field form-address__field_detail form__field">
                                        <input id="" type="text" class="form__it it" data-required="true" name="" value="">
                                        <label for="" class="form__placeholder placeholder placeholder_str">Квартира</label>
                                    </div>
                                </div>

                                <?php /*
                                <div class="order-delivery-info-address">
                                    <?= $helper->render('order/common/smartaddress') ?>
                                </div>
                                */ ?>
                            </div>

                            <?= $helper->render('order/_payment.methods', ['order' => $order]) ?>

                            <? if (isset($order->possible_payment_methods[PaymentMethod::PAYMENT_CARD_ON_DELIVERY]) && !\App::abTest()->isOnlineMotivation(count($orderDelivery->orders))) : ?>
                                <? $checked = $order->payment_method_id == PaymentMethod::PAYMENT_CARD_ON_DELIVERY; ?>
                                <input type="checkbox" class="custom-input custom-input_check jsCreditCardPayment" id="creditCardsPay-<?= $order->block_name ?>" name="" value="" <?= $checked ? 'checked ' : '' ?>/>
                                <label class="order-check custom-label <?= $checked ? 'mChecked ' : '' ?>" for="creditCardsPay-<?= $order->block_name ?>">
                                    Оплата курьеру банковской картой <img class="order-check__img" src="/styles/order/img/i-visa.png" alt=""><img class="order-check__img" src="/styles/order/img/i-mc.png" alt="">
                                </label>
                            <? endif; ?>

                        <? endif ?>

                        <?
                        $dataPoints = (new \View\PointsMap\MapView())->preparePointsWithOrder($order, $orderDelivery);
                        $dataPoints->uniqueCosts = $dataPoints->getUniquePointCosts();
                        $dataPoints->uniqueDays = $dataPoints->getUniquePointDays();
                        $dataPoints->uniqueTokens = $dataPoints->getUniquePointTokens();
                        echo \App::helper()->jsonInScriptTag($dataPoints, '', 'js-points-data');
                        ?>

                        <!--/ способ доставки -->
                        <? if (isset($order->possible_payment_methods[PaymentMethod::PAYMENT_CREDIT]) && !\App::abTest()->isOnlineMotivation(count($orderDelivery->orders))) : ?>
                            <? $checked = $order->payment_method_id == PaymentMethod::PAYMENT_CREDIT; ?>
                            <input type="checkbox" class="custom-input custom-input_checkbox jsCreditPayment" id="credit-<?= $order->block_name ?>" name="" value="" <?= $checked ? 'checked' : '' ?>>
                            <label class="order-check custom-label <?= $checked ? 'mChecked' : '' ?>" for="credit-<?= $order->block_name ?>"><span class="dotted">Купить в кредит</span><!--, от 2 223 &thinsp;<span class="rubl">C</span> в месяц--></label>
                        <? endif; ?>
                    </div>
                </div>
                <!--/ информация о доставке -->

                <!-- информация о заказе -->
                <div class="checkout-order__left">
                    <div class="checkout-order__head order-head">
                        <span class="checkout-order__number">Заказ №<?= ($i) ?></span>
                        <? if ($order->seller): ?>
                            <span class="checkout-order__vendor">продавец: <?= $order->seller->name ?> <a class="checkout-order__vendor-offer js-order-oferta-popup-btn" href="<?= $order->seller->offer ?>" data-value="<?= $order->seller->offer ?>" target="_blank"></a></span>
                        <? endif ?>
                    </div>

                    <div class="checkout-order__content">
                        <? if ($order->total_cost > 100000) : ?>
                            <div class="order-delivery-info-warn">
                                Требуется предоплата.<br/>
                                Сумма заказа превышает 100&nbsp;000&nbsp;руб.
                                <a class="order-delivery-info-warn__icon" href="/how_pay" target="_blank"></a>
                            </div>
                        <? endif; ?>

                        <? foreach ($order->products as $product): ?>
                            <div class="order-item js-order-item"
                                 data-product='<?= json_encode(['id' => $product->id]) ?>'>
                                <a href="<?= $product->link ?>" class="order-item__img" target="_blank">
                                    <img class="image" src="<?= $product->getMainImageUrl('product_60') ?>" alt="<?= $helper->escape($product->name) ?>" />
                                </a>

                                <a href="<?= $product->link ?>" target="_blank" class="order-item__name">
                                    <? if ($product->prefix): ?><?= $product->prefix ?><br/><? endif ?>
                                    <?= $product->name_web ?>
                                </a>

                                <div class="order-item__data">
                                    <span class="order-item__data-item order-item__data-item_summ" ><?= $helper->formatPrice($product->original_sum) ?>&thinsp;<span class="rubl">C</span></span>
                                    <span class="order-item__data-item order-item__data-item_count js-show-counter"><?= $product->quantity ?> шт.</span>

                                    <div class="order-item__data-item order-item__data-item_edit js-order-item-counter">
                                        <div data-spinner-for="" class="counter counter_mini">
                                            <button class="counter__btn counter__btn_minus disabled js-counter-minus"></button>
                                            <input name="productQuantity[]" type="text" value="<?= $product->quantity ?>" class="counter__it js-counter-value" data-stock="<?= $product->stock ?>" />
                                            <button class="counter__btn counter__btn_plus js-counter-plus"></button>
                                            <span>шт.</span>
                                        </div>

                                        <a class="order-item__data-item-control js-order-item-counter-apply" href="" data-id="<?= $product->id; ?>" data-block_name="<?= $order->block_name ?>"><span class="dotted">Применить</span></a>
                                        <span class="order-item__data-item-separate">|</span>
                                        <a class="order-item__data-item-control js-order-item-counter-delete" href="" data-id="<?= $product->id; ?>" data-block_name="<?= $order->block_name ?>"><span class="dotted">Удалить товар</span></a>
                                    </div>

                                    <span class="order-item__data-item order-item__data-item_price"><?= $helper->formatPrice($product->original_price) ?>&thinsp;<span class="rubl">C</span></span>
                                    <?= $helper->render('order/_error.order', [ 'orderDelivery' => $orderDelivery, 'order' => $order, 'product' => $product ]) ?>
                                </div>
                            </div>
                        <? endforeach ?>

                        <? if ((bool)$order->discounts || $order->certificate['par'] !== null) : ?>

                            <div class="orderCol_t">Скидки</div>

                            <? foreach ($order->discounts as $discount) : ?>

                                <div class="orderCol_cnt jsOrderV3Discount">
                                    <a href="" class="orderCol_lk">
                                        <img class="orderCol_img" src="/styles/order/img/fishka.png" alt="">
                                    </a>

                                    <div class="orderCol_n">
                                        <?= $discount->name; ?>
                                    </div>

                                    <span class="orderCol_data orderCol_data-summ orderCol_i_data-sale">-<?= $discount->discount ?>&thinsp;<span class="rubl">C</span></span>
                                    <? if ($discount->number !== null) : ?><span class="orderCol_data orderCol_data-del jsDeleteDiscount" data-value="<?= $discount->number ?>">удалить</span><? endif ?>
                                </div>

                            <? endforeach ; ?>

                            <? if ($order->certificate['par'] !== null) : ?>

                                <div class="orderCol_cnt">
                                    <a href="" class="orderCol_lk">
                                        <img class="orderCol_img" src="/styles/order/img/enter.png" alt="">
                                    </a>

                                    <div class="orderCol_n">Подарочный сертификат <?= $order->certificate['par'] ?> руб</div>

                                    <span class="orderCol_data orderCol_data-summ orderCol_data-sale">-<?= $order->certificate['par'] ?>&thinsp;<span class="rubl">C</span></span>
                                    <span class="orderCol_data orderCol_data-del jsDeleteCertificate">удалить</span>
                                </div>

                            <? endif; ?>

                        <? endif; ?>

                        <div class="order-summ">

                            <? $helper->render('order/_discount', [ 'order' => $order ]) ?>

                            <div class="order-summ__right">
                                <span class="order-summ__title"><?= $order->delivery->price == 0 ? 'Бесплатно' : $helper->formatPrice($order->delivery->price).'&thinsp;<span class="rubl">A</span>' ?></span>
                                <span class="order-summ__value"><?= $order->delivery->use_user_address ? 'Доставка' : 'Самовывоз' ?>:</span>

                                <span class="order-summ__title order-summ__color"><?= $helper->formatPrice($order->total_cost) ?>&thinsp;<span class="rubl">A</span></span>
                                <span class="order-summ__value order-summ__color">Итого:</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!--/ информация о заказе -->
            </div>
            <!--/ блок разбиения заказа -->
        <? endforeach ?>

        <div class="checkout-comment">
            <div class="checkout-comment__title dotted js-order-comment">Дополнительные пожелания</div>

            <textarea class="checkout-comment__field textarea js-order-comment-text" style="display: <?= $firstOrder->comment == '' ? 'none': 'block' ?>"><?= $firstOrder->comment ?></textarea>
        </div>

        <div class="orderComplSumm">
            <span class="l">Итого <?= $orderCount ?> <?= $helper->numberChoice($orderCount, ['заказ', 'заказа', 'заказов']) ?> на общую сумму <?= $helper->formatPrice($orderDelivery->total_cost) ?>&thinsp;<span class="rubl">A</span></span>
        </div>

        <div class="orderCompl">
            <form id="js-orderForm" action="<?= $helper->url('orderV3.create') ?>" method="post">

                <div class="label-strict orderCompl_l orderCompl_l-ln">
                    <input type="checkbox" class="custom-input custom-input_check3 jsAcceptAgreement" id="accept" name="" value="" />

                    <label  class="custom-label jsAcceptTerms" for="accept">
                        Я ознакомлен и согласен с информацией о продавце и его офертой
                        <? if ($orderCount == 1) : ?>
                            <span class="orderCompl_l_lk js-order-oferta-popup-btn" data-value="<?= $order->seller->offer ?>">Ознакомиться</span>
                        <? endif; ?>
                    </label>
                </div>

                <button class="orderCompl_btn btn-primary btn-primary_bigger js-order-submit">Оформить</button>
            </form>
        </div>

        <? if (\App::abTest()->isOrderMinSumRestriction() && \App::config()->minOrderSum > $orderDelivery->getProductsSum()) : ?>
            <div class="popup popup-simple deliv-free-popup jsMinOrderSumPopup" style="display: none;">

                <div class="popup_inn">
                    <span class="info">До оформления заказа осталось</span>
                    <span class="remain-sum"><?= \App::config()->minOrderSum - $orderDelivery->getProductsSum() ?>&thinsp;<span class="rubl">C</span></span>
                    <a href="/cart" class="to-cart-lnk">Вернуться в корзину</a>
                </div>
            </div>
        <? endif ?>

    <? if (!$isAjaxRequest) : ?>
        </section>
    <? endif ?>

    <div id="yandex-map-container" class="selShop_r" style="display: none;" data-options="<?= $helper->json($initialMapCords)?>"></div>
    <div id="kladr-config" data-value="<?= $helper->json(\App::config()->kladr ); ?>"></div>
    <div id="region-data" data-value=<?= json_encode(['name' => $region->getName(), 'kladrId' => $region->kladrId], JSON_UNESCAPED_UNICODE); ?>></div>
    <?= App::config()->debug ? $helper->jsonInScriptTag($orderDelivery, 'initialOrderModel') : '' ?>
    <div id="jsUserAddress" data-value="<?= $helper->json($orderDelivery->user_info->address) ?>"></div>

    <?= \App::templating()->render('order/popup.oferta') ?>
    <?= \App::templating()->render('common/popups/delivery.map') ?>

<? };
