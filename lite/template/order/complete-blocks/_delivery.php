<? use \Model\PaymentMethod\PaymentMethod\PaymentMethodEntity; ?>

<? $f  = function (
    \Model\Order\Entity $order
) { ?>

    <div class="single-order <?= $order->isPaid() ? 'orderPaid': '' ?>">
        <div class="single-order-box">
            <div class="single-order-box__head">
                <? if ($order->getAddress()) : ?>
                    Доставка назначена на <?= $order->getDeliveredAt()->format('d.m.Y') ?>
                <? else : ?>
                    Время и место
                <? endif ?>
            </div>
            <div class="single-order_msg_shop markerLst_row">
                <? if ($order->getAddress()) : ?>
                    <span class="markerList_col">
                        <span class="single-order_msg_shop_addr"><?= $order->getAddress() ?></span>
                    </span>
                <? else : ?>
                    <div class="single-order_msg_info info-phrase">
                    Адрес и дату доставки вашего заказа уточнит по&nbsp;телефону наш менеджер.
                    </div>
                <? endif ?>
                <? if ($order->comment) : ?>
                    <span class="markerList_col">
                        <div class="single-order_msg_adding">Дополнительные пожелания:<br/> «<?= $order->comment ?>»</div>
                    </span>
                <? endif ?>
            </div>

            <div class="single-order_msg_info">
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

<? }; return $f;