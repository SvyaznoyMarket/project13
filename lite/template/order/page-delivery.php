<?php

use \Model\PaymentMethod\PaymentMethod\PaymentMethodEntity as PaymentMethod;

return function(
    \Helper\TemplateHelper $helper,
    \Model\OrderDelivery\Entity $orderDelivery,
    $page,
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

    <?= $page->render('order/common/order-head', ['step' => 2]) ?>

    <section id="js-order-content" class="orderCnt jsOrderV3PageDelivery">
        <h1 class="orderCnt_t">Самовывоз и доставка</h1>

        <div>
            Ваш регион: <a href="" class="dotted jsChangeRegion"><?= \App::user()->getRegion()->getName() ?></a> <br/>
            От региона зависят доступные способы получения и оплаты заказов.
        </div>

        <? if ($orderCount != 1) : ?>
            <p class="">Товары будут оформлены как <?= $orderCount ?> <?= $helper->numberChoice($orderCount, ['отдельный заказ', 'отдельных заказа', 'отдельных заказов']) ?></p>
        <? endif; ?>

        <?= $helper->render('order/_error.main', ['error' => $error, 'orderDelivery' => $orderDelivery]) ?>

        <? foreach ($orderDelivery->orders as $order): $i++;?>
            <? if ((bool)$order->validationErrors) : ?>
                <div class="jsOrderValidationErrors" data-value="<?= $helper->json($order->validationErrors) ?>"></div>
            <? endif; ?>
            <!-- блок разбиения заказа -->
            <div class="checkout-order jsOrderRow <?= $order->isPartnerOffer() ? 'jsPartnerOrder' : ''?>" data-block_name="<?= $order->block_name ?>">
                <!-- информация о доставке -->
                <div class="checkout-order__right">
                    <menu class="orderCol_delivrLst order-head">
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
                    <div class="checkout-order__content">
                        <!--<div class="orderCol_date">15 сентября 2014, воскресенье</div>-->
                        <? if ($order->delivery->date): ?>
                            <div class="orderCol_date" data-content="#id-order-changeDate-content-<?= $order->id ?>"><?= mb_strtolower(\Util\Date::strftimeRu('%e %B2 %G, %A', $order->delivery->date->format('U'))) ?></div>
                        <? endif ?>

                        <?= $helper->render('order/_calendar', [
                            'id'                => 'id-order-changeDate-content-' . $order->id,
                            'possible_days'     => $order->possible_days,
                        ]) ?>

                        <? if ((bool)$order->possible_intervals) : ?>
                            <?= $helper->render('order-v3/common/_delivery-interval', ['order' => $order]) ?>
                        <? endif; ?>
                        <!--/ дата доставки -->

                        <!-- способ доставки -->
                        <? if (!$order->delivery->use_user_address): ?>
                            <? $point = $order->delivery->point ? $orderDelivery->points[$order->delivery->point->token]->list[$order->delivery->point->id] : null ?>
                            <!--Добавляем класс orderCol_delivrIn-warn если у нас будет текст-предупреждение: -->
                            <div class="orderCol_delivrIn orderCol_delivrIn-warn <?= $order->delivery->point ? 'orderCol_delivrIn-pl' : 'orderCol_delivrIn-empty' ?>">

                                <? if (!$order->delivery->point) : ?>
                                    <span class="js-order-changePlace-link dotted" style="cursor: pointer;" data-content="#id-order-changePlace-content-<?= $order->id ?>">Указать место самовывоза</span>
                                <? else : ?>
                                    <div class="orderCol_delivrIn_t">
                                        <strong><?= @$order->delivery->delivery_method->name ?></strong>
                                        <span class="js-order-changePlace-link orderChange dotted" data-content="#id-order-changePlace-content-<?= $order->id ?>">изменить место</span>
                                    </div>
                                <? endif; ?>

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
                            <div class="orderCol_delivrIn orderCol_delivrIn-empty jsSmartAddressBlock">
                                <div class="orderCol_delivrIn_t">
                                    <strong>Адрес</strong> <span class="colorBrightGrey">для всех заказов с доставкой</span>
                                </div>

                                <div class="orderCol_addrs" style="margin-left: 0;">
                                    <?= $helper->render('order-v3/common/_smartaddress') ?>
                                </div>

                            </div>

                            <?= \App::abTest()->isOnlineMotivation(count($orderDelivery->orders)) ? $helper->render('order/_payment.methods', ['order' => $order]) : '' ?>

                            <? if (isset($order->possible_payment_methods[PaymentMethod::PAYMENT_CARD_ON_DELIVERY]) && !\App::abTest()->isOnlineMotivation(count($orderDelivery->orders))) : ?>

                                <div class="orderCheck" style="margin-bottom: 0;">
                                    <? $checked = $order->payment_method_id == PaymentMethod::PAYMENT_CARD_ON_DELIVERY; ?>
                                    <input type="checkbox" class="custom-input custom-input_check jsCreditCardPayment js-customInput" id="creditCardsPay-<?= $order->block_name ?>" name="" value="" <?= $checked ? 'checked ' : '' ?>/>
                                    <label  class="custom-label custom-label-checkbox <?= $checked ? 'mChecked ' : '' ?>" for="creditCardsPay-<?= $order->block_name ?>">
                                        <span class="dotted" style="vertical-align: top;">Оплата курьеру банковской картой</span> <img class="orderCheck_img" src="/styles/order/img/i-visa.png" alt=""><img class="orderCheck_img" src="/styles/order/img/i-mc.png" alt="">
                                    </label>
                                </div>

                            <? endif; ?>

                        <? endif ?>

                        <?
                        $dataPoints = (new \View\PointsMap\MapView());
                        $dataPoints->preparePointsWithOrder($order, $orderDelivery);
                        ?>

                        <?= \App::templating()->render('order/common/map', [
                            'dataPoints'    => $dataPoints,
                            'page'          => 'order'
                        ]) ?>

                        <!--/ способ доставки -->
                        <? if (isset($order->possible_payment_methods[PaymentMethod::PAYMENT_CREDIT]) && !\App::abTest()->isOnlineMotivation(count($orderDelivery->orders))) : ?>

                            <div class="orderCheck orderCheck-credit">
                                <? $checked = $order->payment_method_id == PaymentMethod::PAYMENT_CREDIT; ?>
                                <input type="checkbox" class="custom-input custom-input_checkbox jsCreditPayment js-customInput" id="credit-<?= $order->block_name ?>" name="" value="" <?= $checked ? 'checked' : '' ?>>
                                <label class="custom-label custom-label-checkbox <?= $checked ? 'mChecked' : '' ?>" for="credit-<?= $order->block_name ?>"><span class="dotted">Купить в кредит</span><!--, от 2 223 <span class="rubl">p</span> в месяц--></label>
                            </div>

                        <? endif; ?>
                    </div>
                </div>
                <!--/ информация о доставке -->

                <!-- информация о заказе -->
                <div class="checkout-order__left">
                    <div class="checkout-order__head order-head">
                        <span class="checkout-order__number">Заказ №<?= ($i) ?></span>
                        <? if ($order->seller): ?>
                            <span class="checkout-order__vendor">продавец: <?= $order->seller->name ?> <a class="js-order-oferta-popup-btn" href="<?= $order->seller->offer ?>" data-value="<?= $order->seller->offer ?>" target="_blank">?</a></span>
                        <? endif ?>
                    </div>

                    <div class="checkout-order__content">
                        <? if ($order->total_cost > 100000) : ?>
                            <div class="orderCol orderCol_warn"><span class="orderCol_warn_l">Требуется предоплата.</span> <span class="orderCol_warn_r">Сумма заказа превышает 100&nbsp;000&nbsp;руб. <a href="/how_pay" target="_blank">Подробнее</a></span></div>
                        <? endif; ?>

                        <? foreach ($order->products as $product): ?>
                            <div class="order-item">
                                <a href="<?= $product->link ?>" class="order-item__img" target="_blank">
                                    <img class="image" src="<?= $product->getMainImageUrl('product_60') ?>" alt="<?= $helper->escape($product->name) ?>" />
                                </a>

                                <a href="<?= $product->link ?>" target="_blank" class="order-item__name">
                                    <? if ($product->prefix): ?><?= $product->prefix ?><br/><? endif ?>
                                    <?= $product->name_web ?>
                                </a>

                                <div class="order-item__data">
                                    <span class="order-item__data-item order-item__data-item_summ" ><?= $helper->formatPrice($product->original_sum) ?> <span class="rubl">p</span></span>
                                    <span class="order-item__data-item order-item__data-item_count"><?= $product->quantity ?> шт.</span>

                                    <div class="order-item__data-item order-item__data-item_edit" style="display: none">
                                        <div data-spinner-for="" class="bCountSection">
                                            <button class="bCountSection__eM">-</button>
                                            <input name="productQuantity[]" type="text" value="<?= $product->quantity ?>" class="bCountSection__eNum" data-stock="<?= $product->stock ?>" />
                                            <button class="bCountSection__eP">+</button>
                                            <span>шт.</span>
                                        </div>

                                        <a class="dotted jsChangeProductQuantity" href="" data-id="<?= $product->id; ?>" data-block_name="<?= $order->block_name ?>">Применить</a>
                                        &nbsp;|&nbsp;
                                        <a class="dotted jsDeleteProduct" href="" data-id="<?= $product->id; ?>" data-block_name="<?= $order->block_name ?>">Удалить товар</a>
                                    </div>

                                    <span class="order-item__data-item order-item__data-item_price"><?= $helper->formatPrice($product->original_price) ?> <span class="rubl">p</span></span>
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

                                    <span class="orderCol_data orderCol_data-summ orderCol_i_data-sale">-<?= $discount->discount ?> <span class="rubl">p</span></span>
                                    <? if ($discount->number !== null) : ?><span class="orderCol_data orderCol_data-del jsDeleteDiscount" data-value="<?= $discount->number ?>">удалить</span><? endif ?>
                                </div>

                            <? endforeach ; ?>

                            <? if ($order->certificate['par'] !== null) : ?>

                                <div class="orderCol_cnt">
                                    <a href="" class="orderCol_lk">
                                        <img class="orderCol_img" src="/styles/order/img/enter.png" alt="">
                                    </a>

                                    <div class="orderCol_n">Подарочный сертификат <?= $order->certificate['par'] ?> руб</div>

                                    <span class="orderCol_data orderCol_data-summ orderCol_data-sale">-<?= $order->certificate['par'] ?> <span class="rubl">p</span></span>
                                    <span class="orderCol_data orderCol_data-del jsDeleteCertificate">удалить</span>
                                </div>

                            <? endif; ?>

                        <? endif; ?>

                        <div class="order-summ">

                            <?= $helper->render('order/_discount', [ 'order' => $order ]) ?>

                            <div class="order-summ__right">
                                <span class="order-summ__title order-summ__title_color"><?= $order->delivery->price == 0 ? 'Бесплатно' : $helper->formatPrice($order->delivery->price).' <span class="rubl">p</span>' ?></span>
                                <span class="order-summ__value"><?= $order->delivery->use_user_address ? 'Доставка' : 'Самовывоз' ?>:</span>

                                <span class="order-summ__title"><?= $helper->formatPrice($order->total_cost) ?> <span class="rubl">p</span></span>
                                <span class="order-summ__value">Итого:</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!--/ информация о заказе -->
            </div>
            <!--/ блок разбиения заказа -->
        <? endforeach ?>

        <div class="orderComment">
            <div class="orderComment_t jsOrderV3Comment">Дополнительные пожелания</div>

            <textarea class="orderComment_fld textarea" style="display: <?= $firstOrder->comment == '' ? 'none': 'block' ?>"><?= $firstOrder->comment ?></textarea>
        </div>

        <div class="orderComplSumm">
            <span class="l">Итого <strong><?= $orderCount ?></strong> <?= $helper->numberChoice($orderCount, ['заказ', 'заказа', 'заказов']) ?> на общую сумму <strong><?= $helper->formatPrice($orderDelivery->total_cost) ?> <span class="rubl">p</span></strong></span>
        </div>

        <div class="orderCompl">
            <form id="js-orderForm" action="<?= $helper->url('orderV3.create') ?>" method="post">

                <div class="orderCompl_l orderCompl_l-ln orderCheck orderCheck-str">
                    <input type="checkbox" class="custom-input custom-input_check3 js-customInput jsAcceptAgreement" id="accept" name="" value="" />

                    <label  class="custom-label jsAcceptTerms" for="accept">
                        Я ознакомлен и согласен с информацией о продавце и его офертой
                        <? if ($orderCount == 1) : ?>
                            <span class="orderCompl_l_lk js-order-oferta-popup-btn" data-value="<?= $order->seller->offer ?>">Ознакомиться</span>
                        <? endif; ?>
                    </label>
                </div>

                <button class="orderCompl_btn btn-primary btn-primary_bigger">Оформить</button>
            </form>
        </div>

        <? if (\App::abTest()->isOrderMinSumRestriction() && \App::config()->minOrderSum > $orderDelivery->getProductsSum()) : ?>
            <div class="popup popup-simple deliv-free-popup jsMinOrderSumPopup" style="display: none;">

                <div class="popup_inn">
                    <span class="info">До оформления заказа осталось</span>
                    <span class="remain-sum"><?= \App::config()->minOrderSum - $orderDelivery->getProductsSum() ?>&thinsp;<span class="rubl">p</span></span>
                    <a href="/cart" class="to-cart-lnk">Вернуться в корзину</a>
                </div>
            </div>
        <? endif ?>

    </section>

    <div id="yandex-map-container" class="selShop_r" style="display: none;" data-options="<?= $helper->json($initialMapCords)?>"></div>
    <div id="kladr-config" data-value="<?= $helper->json(\App::config()->kladr ); ?>"></div>
    <div id="region-name" data-value=<?= json_encode($region->getName(), JSON_UNESCAPED_UNICODE); ?>></div>
    <?= App::config()->debug ? $helper->jsonInScriptTag($orderDelivery, 'initialOrderModel') : '' ?>
    <div id="jsUserAddress" data-value="<?= $helper->json($orderDelivery->user_info->address) ?>"></div>

    <div class="popup popup-simple js-order-oferta-popup">
        <a href="" class="close"></a>

        <div class="popup_inn">
            <div class="orderOferta">
                <div class="orderOferta_tabs">
                    <div class="orderOferta_tabs_i orderOferta_tabs_i-cur js-oferta-tab" data-tab="tab-1">Условия продажи</div>
                    <div class="orderOferta_tabs_i js-oferta-tab" data-tab="tab-2">Правовая информация</div>
                </div>

                <div class="orderOferta_tabcnt orderOferta_tabcnt-cur js-tab-oferta-content" id="tab-1">
                    <div class="orderOferta_tl">Условия продажи</div>
                </div>

                <div class="orderOferta_tabcnt js-tab-oferta-content" id="tab-2">
                    <div class="orderOferta_tl">Правовая информация для пользователей сайта ООО «Энтер»</div>

                    <p>ООО «Энтер» оставляет за собой право изменять публикуемые на данном сайте материалы в любое время без предварительного уведомления.</p>

                    <div class="orderOferta_tl">Авторские права</div>
                    <p>Информация сайта ООО «Энтер» защищена авторским правом и действующим законодательством о защите интеллектуальной собственности. ООО «Энтер» предоставляет право посетителям сайта использовать опубликованные материалы в любых личных и некоммерческих целях. Любое копирование, изменение или использование данных материалов в коммерческих целях допускается с письменного согласия ООО «Энтер».</p>

                    <div class="orderOferta_tl">Содержание материалов</div>
                    <p>ООО «Энтер» приняты все разумные меры к тому, чтобы обеспечить точность и актуальность размещенной на этом сайте информации. ООО «Энтер» оставляет за собой право вносить изменение в содержание материалов этого сайта в любое время по собственному усмотрению. Продукты или услуги, не относящиеся напрямую к ООО «Энтер», упомянуты исключительно в информационных целях. Вся информация об ООО «Энтер» и третьих сторонах на этом сайте представлена в том виде, в котором она существует в распоряжении ООО «Энтер».</p>

                    <div class="orderOferta_tl">Товарные знаки</div>
                    <p>Товарные знаки, торговые марки, а также другие средства индивидуализации, помещенные на данном сайте, являются собственностью ООО «Энтер» и третьих лиц. Информация, опубликованная на сайте, не предоставляет никаких лицензионных прав на использование любых товарных знаков без получения предварительного письменного согласия владельца.</p>

                    <div class="orderOferta_tl">Ссылки</div>
                    <p>На данном сайте Вы можете найти ссылки на другие (внешние) сайты, не принадлежащие ООО «Энтер». Информация с этих сайтов не является продолжением или дополнением материалов ООО «Энтер».</p>

                    <div class="orderOferta_tl">Реквизиты</div>
                    <p>Наименование: ООО «Энтер» Место нахождения: 115419, г. Москва, улица Орджоникидзе, дом 11, строение 10. Государственный регистрационный номер записи о создании юридического лица: 1117746043381 ИНН: 7710881860 КПП: 772501001 Наименование банка: Связной Банк ЗАО г. Москва БИК: 044583139 Расчетный счет: 40702810400000002751</p>
                </div>
            </div>
        </div>
    </div>

<? };