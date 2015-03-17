<? use \Model\PaymentMethod\PaymentMethod\PaymentMethodEntity; ?>

<? $f  = function (
    \Helper\TemplateHelper $helper,
    \Model\Order\Entity $order,
    $point
) {
    /** @var $point \Model\Shop\Entity */
    ?>

<div class="orderPayment <?= $order->isPaid() ? 'orderPaid jsOrderPaid': '' ?>">
    <div class="orderPayment_block orderPayment_noOnline">

        <? if ((bool)$point) : ?>

            <div class="orderPayment_msg orderPayment_noOnline_msg">
                <div class="orderPayment_msg_head">
                    Ждем вас <?= $order->getDeliveredAt()->format('d.m.Y') ?> в магазине
                </div>
                <div class="orderPayment_msg_shop markerLst_row">

                    <? if ((bool)$point->getSubway()) : ?>

                        <span class="markerList_col markerList_col-mark">
                            <i class="markColor" style="background-color: <?= $point->getSubway()[0]->getLine()->getColor() ?>"></i>
                        </span>
                        <span class="markerList_col">
                            <span class="orderPayment_msg_shop_metro"><?= $point->getSubway()[0]->getName() ?></span>

                    <? endif ?>

                        <span class="orderPayment_msg_shop_addr"><?= $point->getAddress() ?></span>
                        <a href="<?= \App::router()->generate('shop.show', ['regionToken' => \App::user()->getRegion()->getToken(), 'shopToken' => $point->getToken()])?>" class="orderPayment_msg_addr_link jsCompleteOrderShowShop" target="_blank">
                            Как добраться
                        </a>
                    </span>

                    <? if ($order->comment) : ?>
                        <div class="orderPayment_msg_adding">Дополнительные пожелания:<br/> «<?= $order->comment ?>»</div>
                    <? endif ?>
                </div>

                <div class="orderPayment_msg_info">
                    <? if ($order->isPaid()) : ?>
                        Заказ оплачен
                    <? elseif ($order->isPaidBySvyaznoy()) : ?>
                        <div class="orderPayment_msg_info">
                            Вы успешно применили плюсы <img src="/styles/order/img/sclub-complete.jpg"><br/>
                            <b>Не забудьте</b> взять карту «Связной Клуб» в магазин!
                        </div>
                        Сумма к оплате: <b><?= $helper->formatPrice($order->getSum() - $order->getSvyaznoyPaymentSum()) ?></b> <span class="rubl">р</span><br />
                        Оплата при получении — наличными или картой.
                    <? elseif (in_array($order->getPaymentId(), [
                        PaymentMethodEntity::PAYMENT_PAYPAL,
                        PaymentMethodEntity::PAYMENT_CARD_ONLINE,
                        PaymentMethodEntity::PAYMENT_PSB
                    ])) : ?>
                        Вы можете оплатить заказ при получении.
                    <? else : ?>
                        Оплата при получении — наличными или картой.
                    <? endif ?>
                </div>
            </div>

        <? else : ?>

            <div class="orderPayment_msg orderPayment_noOnline_msg">
                <div class="orderPayment_msg_head">
                    Время и место
                </div>
                <span class="markerList_col">
                    Адрес и дату доставки вашего заказа уточнит по&nbsp;телефону наш менеджер.
                </span>
                <? if ($order->comment) : ?>
                    <div class="orderPayment_msg_adding">Дополнительные пожелания:<br/> «<?= $order->comment ?>»</div>
                <? endif ?>
                <div class="orderPayment_msg_info">
                    <? if ($order->isPaid()) : ?>
                        Заказ оплачен
                    <? else : ?>
                        Вы сможете оплатить заказ при получении.
                    <? endif ?>
                </div>
            </div>

        <? endif ?>

    </div>
</div>

<? }; return $f;