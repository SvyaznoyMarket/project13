<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\OrderDelivery\Entity $orderDelivery
) {
    $orderCount = count($orderDelivery->orders);


?>

<?= $helper->render('order-v3/__head', ['step' => 2]) ?>

<section class="orderCnt">
    <h1 class="orderCnt_t">Самовывоз и доставка</h1>
    <!-- заголовок страницы -->

    <p class="orderInf">Товары будут оформлены как <strong><?= $orderCount ?> <?= $helper->numberChoice($orderCount, ['отдельный заказ', 'отдельных заказа', 'отдельных заказов']) ?></strong></p>

    <div class="orderInf clearfix">
        <div>Ваш регион: <strong><?= \App::user()->getRegion()->getName() ?></strong></div>

        <div class="fl-l">От региона зависят доступные способы получения и оплаты заказов.</div>

        <button class="btnLightGrey fl-r">Изменить регион</button>
    </div>

    <? foreach ($orderDelivery->orders as $i => $order): ?>
    <!-- блок разбиения заказа -->
    <div class="orderRow clearfix">
        <!-- информация о заказе -->
        <div class="orderCol">
            <div class="orderCol_h">
                <strong>Заказ №<?= ($i + 1) ?></strong>
                <? if ($order->seller): ?>
                    <span class="colorBrightGrey">продавец: <?= $order->seller->name ?></span>
                <? endif ?>
            </div>

            <? foreach ($order->products as $product): ?>
            <div class="orderCol_cnt clearfix">
                <a href="" class="orderCol_lk">
                    <img class="orderCol_img" src="<?= $product->image ?>" alt="<?= $helper->escape($product->name) ?>" />
                </a>

                <a href="" class="orderCol_n">
                    <? if ($product->prefix): ?><?= $product->prefix ?><br/><? endif ?>
                    <?= $product->name_web ?>
                </a>

                <span class="orderCol_data orderCol_data-summ"><?= $helper->formatPrice($product->sum) ?> <span class="rubl">p</span></span>
                <span class="orderCol_data orderCol_data-count"><?= $product->quantity ?> шт.</span>
                <span class="orderCol_data orderCol_data-price"><?= $helper->formatPrice($product->price) ?> <span class="rubl">p</span></span>
            </div>
            <? endforeach ?>

            <div class="orderCol_f clearfix">
                <div class="orderCol_f_l">
                    <span class="orderCol_f_t brb-dt">Ввести код скидки</span>
                </div>

                <div class="orderCol_f_r">
                    <span class="orderCol_summ"><?= $helper->formatPrice($order->delivery->price) ?> <span class="rubl">p</span></span>
                    <span class="orderCol_summt orderCol_summt-m">Доставка:</span>

                    <span class="orderCol_summ"><?= $helper->formatPrice($order->total_cost) ?> <span class="rubl">p</span></span>
                    <span class="orderCol_summt">Итого:</span>
                </div>
            </div>
        </div>
        <!--/ информация о заказе -->

        <!-- информация о доставке -->
        <div class="orderCol orderCol-r">
            <menu class="orderCol_delivrLst">
            <? foreach ($order->possible_delivery_groups as $deliveryGroupId): ?>
                <? $deliveryGroup = $orderDelivery->delivery_groups[$deliveryGroupId] ?>
                <li class="orderCol_delivrLst_i <? if ($deliveryGroupId == $order->delivery_group_id): ?>orderCol_delivrLst_i-act<? endif ?>"><?= $deliveryGroup->name ?></li>
            <? endforeach ?>
            </menu>

            <!-- дата доставки -->
            <div class="orderCol_delivrIn clearfix">
                <!--<div class="orderCol_date">15 сентября 2014, воскресенье</div>-->
                <? if ($order->delivery && $order->delivery->date): ?>
                    <div class="orderCol_date"><?= mb_strtolower(\Util\Date::strftimeRu('%e %B2 %G, %A', $order->delivery->date)) ?></div>
                <? endif ?>
                <span class="orderChange">изменить дату</span>
            </div>
            <!--/ дата доставки -->

            <!-- способ доставки -->
            <? if ($order->delivery && !$order->delivery->use_user_address): ?>
                <? $point = $order->delivery->point ? $orderDelivery->points[$order->delivery->point->token]->list[$order->delivery->point->id] : null ?>

            <div class="orderCol_delivrIn orderCol_delivrIn-pl">
                <div class="orderCol_delivrIn_t clearfix">
                    <strong><?= $orderDelivery->points[$order->delivery->point->token]->block_name ?></strong>

                    <span class="orderChange">изменить место</span>
                </div>

                <div class="orderCol_addrs"<? if (isset($point->subway[0]->line)): ?> style="background: <?= $point->subway[0]->line->color ?>;"<? endif ?>>
                    <span class="orderCol_addrs_tx">
                        <? if (isset($point->subway[0])): ?><?= $point->subway[0]->name ?><br/><? endif ?>
                        <span class="colorBrightGrey"><?= $point->address ?></span>
                    </span>
                </div>

                <div class="orderCol_tm">
                    <? if ($point->regtime): ?><span class="orderCol_tm_t">Режим работы:</span> <?= $point->regtime ?><? endif ?>
                </div>
            </div>
            <? endif ?>
            <!--/ способ доставки -->
        </div>
        <!--/ информация о доставке -->
    </div>
    <!--/ блок разбиения заказа -->
    <? endforeach ?>

    <div class="orderComment">
        <div class="orderComment_t">Дополнительные пожелания</div>

        <textarea class="orderComment_fld textarea"></textarea>
    </div>

    <div class="orderCompl clearfix">
        <p class="orderCompl_l">
            <span class="l">Итого <strong><?= $orderCount ?></strong> <?= $helper->numberChoice($orderCount, ['заказ', 'заказа', 'заказов']) ?> на общую сумму <strong><?= $helper->formatPrice($orderDelivery->total_cost) ?> <span class="rubl">p</span></strong></span>
            <span class="colorBrightGrey dblock">Вы сможете заполнить заявку на кредит и оплатить онлайн на следующем шаге</span>
        </p>

        <form id="js-orderForm" action="<?= $helper->url('orderV3.create') ?>" method="post">
            <button class="orderCompl_btn btnsubmit">Оформить ➜</button>
        </form>
    </div>
</section>

<? };
