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

            <div class="single-order-box__shop shop-info">
                <? if ($order->getAddress()) : ?>
                    <div class="shop-info__address"><?= $order->getAddress() ?></div>
                <? else : ?>
                    <div class="shop-info__address">
                    Адрес и дату доставки вашего заказа уточнит по&nbsp;телефону наш менеджер.
                    </div>
                <? endif ?>
                <? if ($order->comment) : ?>
                    <div class="shop-info__address">
                        Дополнительные пожелания:<br/> «<?= $order->comment ?>»
                    </div>
                <? endif ?>
            </div>

            <div class="single-order-box__info">
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