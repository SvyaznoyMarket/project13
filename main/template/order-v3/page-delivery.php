<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\OrderDelivery\Entity $orderDelivery,
    $error = null
) {
    $orderCount = count($orderDelivery->orders);
    $region = \App::user()->getRegion();
    $regionName = $region->getName();
    $firstOrder = reset($orderDelivery->orders);
    $i = 0;

    $isCoordsValid = $region && $region->getLatitude() != null && $region->getLongitude() != null;

    $initialMapCords = [
        'latitude' => $isCoordsValid ? $region->getLatitude() : 55.76,
        'longitude' => $isCoordsValid ? $region->getLongitude() : 37.64,
        'zoom' => $isCoordsValid ? 10 : 4
    ];


?>

<?= $helper->render('order-v3/__head', ['step' => 2]) ?>

<section id="js-order-content" class="orderCnt jsOrderV3PageDelivery">
    <h1 class="orderCnt_t">Самовывоз и доставка</h1>

    <?= $helper->render('order-v3/__error', ['error' => $error]) ?>

    <? if ($orderCount != 1) : ?>
        <p class="orderInf">Товары будут оформлены как <strong><?= $orderCount ?> <?= $helper->numberChoice($orderCount, ['отдельный заказ', 'отдельных заказа', 'отдельных заказов']) ?></strong></p>
    <? endif; ?>

    <div class="orderInf clearfix">
        <div class="fl-l">Ваш регион: <strong><?= \App::user()->getRegion()->getName() ?></strong> <br/> 
        От региона зависят доступные способы получения и оплаты заказов.</div>

        <button class="btnLightGrey orderCnt_btn fl-r jsChangeRegion">Изменить регион</button>
    </div>

    <? foreach ($orderDelivery->orders as $order): $i++;?>
        <? if ((bool)$order->validationErrors) : ?>
            <div class="jsOrderValidationErrors" data-value="<?= $helper->json($order->validationErrors) ?>"></div>
        <? endif; ?>
    <!-- блок разбиения заказа -->
    <div class="orderRow clearfix" data-block_name="<?= $order->block_name ?>">
        <!-- информация о заказе -->
        <div class="orderCol">
            <div class="orderCol_h">
                <strong class="orderNum">Заказ №<?= ($i) ?></strong>
                <? if ($order->seller): ?>
                    <span class="orderDetl">продавец: <?= $order->seller->name ?> <a class="orderDetl_lk" href="<?= $order->seller->offer ?>" target="_blank">Информация и оферта</a></span>
                <? endif ?>
            </div>

            <? if ($order->total_cost > 100000) : ?>
                <div class="orderCol orderCol_warn"><span class="orderCol_warn_l">Требуется предоплата.</span> <span class="orderCol_warn_r">Сумма заказа превышает 100&nbsp;000&nbsp;руб. <a href="/how_pay" target="_blank">Подробнее</a></span></div>
            <? endif; ?>

            <? foreach ($order->products as $product): ?>
            <div class="orderCol_cnt clearfix">
                <a href="<?= $product->link ?>" class="orderCol_lk" target="_blank">
                    <img class="orderCol_img" src="<?= $product->getMainImageUrl('product_60') ?>" alt="<?= $helper->escape($product->name) ?>" />
                </a>

                <a href="<?= $product->link ?>" target="_blank" class="orderCol_n">
                    <? if ($product->prefix): ?><?= $product->prefix ?><br/><? endif ?>
                    <?= $product->name_web ?>
                </a>

                <span class="orderCol_data orderCol_data-summ" ><?= $helper->formatPrice($product->original_sum) ?> <span class="rubl">p</span></span>
                <span class="orderCol_data orderCol_data-count"><?= $product->quantity ?> шт.</span>

                <div class="orderCol_data orderCol_data-edit" style="display: none">
                    <div data-spinner-for="" class="bCountSection clearfix">
                        <button class="bCountSection__eM">-</button>
                        <input name="productQuantity[]" type="text" value="<?= $product->quantity ?>" class="bCountSection__eNum" data-stock="<?= $product->stock ?>" />
                        <button class="bCountSection__eP">+</button>
                        <span>шт.</span>
                    </div>

                    <a class="brb-dt jsChangeProductQuantity" href="" data-id="<?= $product->id; ?>" data-block_name="<?= $order->block_name ?>">Применить</a>
                    &nbsp;|&nbsp;
                    <a class="brb-dt jsDeleteProduct" href="" data-id="<?= $product->id; ?>" data-block_name="<?= $order->block_name ?>">Удалить товар</a>
                </div>

                <span class="orderCol_data orderCol_data-price"><?= $helper->formatPrice($product->original_price) ?> <span class="rubl">p</span></span>
                <?= $helper->render('order-v3/__errors', [ 'orderDelivery' => $orderDelivery, 'order' => $order, 'product' => $product ]) ?>
            </div>
            <? endforeach ?>

            <? if ((bool)$order->discounts || $order->certificate['par'] !== null) : ?>

                <div class="orderCol_t">Скидки</div>

                <? foreach ($order->discounts as $discount) : ?>

                    <div class="orderCol_cnt clearfix">
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

                    <div class="orderCol_cnt clearfix">
                        <a href="" class="orderCol_lk">
                            <img class="orderCol_img" src="/styles/order/img/enter.png" alt="">
                        </a>

                        <div class="orderCol_n">Подарочный сертификат <?= $order->certificate['par'] ?> руб</div>

                        <span class="orderCol_data orderCol_data-summ orderCol_data-sale">-<?= $order->certificate['par'] ?> <span class="rubl">p</span></span>
                        <span class="orderCol_data orderCol_data-del jsDeleteCertificate">удалить</span>
                    </div>

                <? endif; ?>

            <? endif; ?>

            <div class="orderCol_f clearfix">

                <?= $helper->render('order-v3/__discount', [ 'order' => $order ]) ?>

                <div class="orderCol_f_r">
                    <span class="orderCol_summ"><?= $order->delivery->price == 0 ? 'Бесплатно' : $helper->formatPrice($order->delivery->price).' <span class="rubl">p</span>' ?></span>
                    <span class="orderCol_summt orderCol_summt-m"><?= $order->delivery->use_user_address ? 'Доставка' : 'Самовывоз' ?>:</span>

                    <span class="orderCol_summ"><?= $helper->formatPrice($order->total_cost) ?> <span class="rubl">p</span></span>
                    <span class="orderCol_summt">Итого:</span>
                </div>

                <? if (isset($order->possible_payment_methods[\Model\PaymentMethod\PaymentMethod\PaymentMethodEntity::PAYMENT_CREDIT])) : ?>

                    <div class="orderCheck orderCheck-credit clearfix">
                        <? $checked = $order->payment_method_id == \Model\PaymentMethod\PaymentMethod\PaymentMethodEntity::PAYMENT_CREDIT; ?>
                        <input type="checkbox" class="customInput customInput-checkbox jsCreditPayment" id="credit-<?= $order->block_name ?>" name="" value="" <?= $checked ? 'checked' : '' ?>>
                        <label class="customLabel <?= $checked ? 'mChecked' : '' ?>" for="credit-<?= $order->block_name ?>">Купить в кредит<!--, от 2 223 <span class="rubl">p</span> в месяц--></label>
                    </div>

                <? endif; ?>

            </div>
        </div>
        <!--/ информация о заказе -->

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
            <div class="orderCol_delivrIn clearfix">
                <? if ($order->delivery->date): ?>
                    <div class="orderCol_date" data-content="#id-order-changeDate-content-<?= $order->id ?>"><?= mb_strtolower(\Util\Date::strftimeRu('%e %B2 %G, %A', $order->delivery->date->format('U'))) ?></div>
                <? endif ?>

                <?= $helper->render('order-v3/__calendar', [
                    'id'                => 'id-order-changeDate-content-' . $order->id,
                    'possible_days'     => $order->possible_days,
                ]) ?>

                <? if ((bool)$order->possible_intervals) : ?>
                    <?= $helper->render('order-v3/common/_delivery-interval', ['order' => $order]) ?>
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
                <div class="orderCol_delivrIn">
                    <div class="orderCol_delivrIn_t clearfix">
                        <strong>Адрес</strong> <span class="colorBrightGrey">для всех заказов с доставкой</span>
                    </div>

                    <div class="orderCol_addrs">
                        <?= $helper->render('order-v3/common/_smartaddress') ?>
                    </div>

                </div>

                <? if (isset($order->possible_payment_methods[\Model\PaymentMethod\PaymentMethod\PaymentMethodEntity::PAYMENT_CARD_ON_DELIVERY])) : ?>

                    <div class="orderCheck mb10">
                        <? $checked = $order->payment_method_id == \Model\PaymentMethod\PaymentMethod\PaymentMethodEntity::PAYMENT_CARD_ON_DELIVERY; ?>
                        <input type="checkbox" class="customInput customInput-checkbox jsCreditCardPayment" id="creditCardsPay-<?= $order->block_name ?>" name="" value="" <?= $checked ? 'checked ' : '' ?>/>
                        <label  class="customLabel <?= $checked ? 'mChecked ' : '' ?>" for="creditCardsPay-<?= $order->block_name ?>">
                            Оплата банковской картой
                            <span class="dblock colorBrightGrey s">Иначе курьер сможет принять только наличные</span>
                        </label>
                    </div>

                <? endif; ?>

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

    <div class="orderComment">
        <div class="orderComment_t">Дополнительные пожелания</div>

        <textarea class="orderComment_fld textarea" style="display: <?= $firstOrder->comment == '' ? 'none': 'block' ?>"><?= $firstOrder->comment ?></textarea>
    </div>



    <div class="orderComplSumm">
        <span class="l">Итого <strong><?= $orderCount ?></strong> <?= $helper->numberChoice($orderCount, ['заказ', 'заказа', 'заказов']) ?> на общую сумму <strong><?= $helper->formatPrice($orderDelivery->total_cost) ?> <span class="rubl">p</span></strong></span>
    </div>

    <div class="orderCompl clearfix">

        <?= \App::templating()->render('order-v3/common/_blackfriday', ['version' => 1]) ?>

        <form id="js-orderForm" action="<?= $helper->url('orderV3.create') ?>" method="post">

            <div class="orderCompl_l orderCompl_l-ln orderCheck orderCheck-str">
                <input type="checkbox" class="customInput customInput-checkbox jsAcceptAgreement" id="accept" name="" value="" />

                <label  class="customLabel jsAcceptTerms" for="accept">
                    Я ознакомлен и согласен с информацией о продавце и его офертой
                </label>
            </div>

            <button class="orderCompl_btn btnsubmit">Оформить ➜</button>
        </form>
    </div>

</section>

<div id="yandex-map-container" class="selShop_r" style="display: none;" data-options="<?= $helper->json($initialMapCords)?>"></div>
<div id="kladr-config" data-value="<?= $helper->json(\App::config()->kladr ); ?>"></div>
<div id="region-name" data-value=<?= json_encode($region->getName(), JSON_UNESCAPED_UNICODE); ?>></div>
<? if (App::config()->debug) : ?><div id="initialOrderModel" data-value="<?= $helper->json($orderDelivery) ?>"></div><? endif; ?>
<div id="jsUserAddress" data-value="<?= $helper->json($orderDelivery->user_info->address) ?>"></div>
<? };
