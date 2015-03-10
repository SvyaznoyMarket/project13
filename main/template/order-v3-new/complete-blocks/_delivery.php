<? use \Model\PaymentMethod\PaymentMethod\PaymentMethodEntity; ?>

<? $f  = function (
    \Model\Order\Entity $order
) { ?>

    <div class="orderPayment orderDelivery <?= $order->isPaid() ? 'orderPaid': '' ?>">
        <div class="orderPayment_block orderPayment_noOnline">

            <div class="orderPayment_msg orderPayment_noOnline_msg">
                <div class="orderPayment_msg_head">
                    <? if ($order->getAddress()) : ?>
                        Доставка назначена на <?= $order->getDeliveredAt()->format('d.m.Y') ?>
                    <? else : ?>
                        Время и место
                    <? endif ?>
                </div>
                <div class="orderPayment_msg_shop markerLst_row">
                    <span class="markerList_col">
                        <? if ($order->getAddress()) : ?>
                            <span class="orderPayment_msg_shop_addr"><?= $order->getAddress() ?></span>
                        <? else : ?>
                            Адрес и дату доставки вашего заказа уточнит по&nbsp;телефону наш менеджер.
                        <? endif ?>
                        <? if ($order->comment) : ?>
                            <div class="orderPayment_msg_adding">Дополнительные пожелания:<br/> «<?= $order->comment ?>»</div>
                        <? endif ?>
                    </span>
                </div>

                <div class="orderPayment_msg_info">
                    <? if ($order->isPaid()) : ?>
                        Заказ оплачен
                    <? elseif ($order->getPaymentId() == PaymentMethodEntity::PAYMENT_CARD_ON_DELIVERY) : ?>
                        Оплата заказа банковской картой при получении.
                    <? else : ?>
                        Вы можете оплатить заказ при получении.
                    <? endif ?>
                </div>

            </div>
        </div>
    </div>

<? }; return $f;