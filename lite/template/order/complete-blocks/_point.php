<? use \Model\PaymentMethod\PaymentMethod\PaymentMethodEntity; ?>

<? $f  = function (
    \Helper\TemplateHelper $helper,
    \Model\Order\Entity $order
) {
    ?>

<div class="single-order <?= $order->isPaid() ? 'orderPaid jsOrderPaid': '' ?>">
    <div class="single-order-box">

        <? if ((bool)$order->point) : ?>
            <div class="single-order-box__head">
                <? if ($order->point->isEnterShop() || $order->point->isSvyaznoyShop() || $order->point->isEurosetPoint()) : ?>
                    Ждем вас <?= $order->getDeliveredAt()->format('d.m.Y') ?> в магазине
                <? elseif ($order->point->isPickpoint()) : ?>
                    Вы можете забрать заказ из постамата <?= $order->getDeliveredAt()->format('d.m.Y') ?>
                <? elseif ($order->point->isHermesPoint()) : ?>
                    Вы можете забрать заказ в пункте выдачи Hermes-DPD <?= $order->getDeliveredAt()->format('d.m.Y') ?>
                <? endif ?>
            </div>

            <div class="single-order-box__shop shop-info">
                <? if ((bool)$order->point->subway) : ?>
                    <div class="shop-info__subway"><i class="shop-info__subway-bullet" style="background-color: <?= $order->point->subway->getLine()->getColor() ?>"></i><?= $order->point->subway->getName() ?></div>
                <? endif ?>

                <div class="shop-info__address"><?= $order->point->address ?></div>
                <? /* if ($order->shop) : ?>
                    <a class="shop-info__address-details underline jsCompleteOrderShowShop" href="<?= \App::router()->generate('shop.show', ['regionToken' => \App::user()->getRegion()->getToken(), 'shopToken' => $order->shop->getToken()])?>" target="_blank">
                        Как добраться
                    </a>
                <? endif */ ?>

                <? if ($order->comment) : ?>
                    <div class="single-order_msg_adding">Дополнительные пожелания:<br/> «<?= $order->comment ?>»</div>
                <? endif ?>
            </div>

            <div class="single-order-box__info">
                <? if ($order->isPaid()) : ?>
                    Заказ оплачен
                <? elseif ($order->isPaidBySvyaznoy()) : ?>
                    <div class="single-order_msg_info">
                        Вы успешно применили плюсы <img src="/styles/order/img/sclub-complete.jpg"><br/>
                        <b>Не забудьте</b> взять карту «Связной Клуб» в магазин!
                    </div>
                    Сумма к оплате: <b><?= $helper->formatPrice($order->getSum() - $order->getSvyaznoyPaymentSum()) ?> <span class="rubl">p</span></b><br />
                    Оплата при получении — наличными или картой.
                <? elseif (in_array($order->getPaymentId(), [
                    PaymentMethodEntity::PAYMENT_PAYPAL,
                    PaymentMethodEntity::PAYMENT_CARD_ONLINE,
                    PaymentMethodEntity::PAYMENT_PSB
                ])) : ?>
                    Вы можете оплатить заказ при получении.
                <? elseif ($order->point && $order->point->isHermesPoint()) : ?>
                    Оплата наличными при получении.
                <? else : ?>
                    Оплата при получении — наличными или картой.
                <? endif ?>
            </div>

        <? else : ?>

            <div class="single-order_msg_head">
                Время и место
            </div>

            <div class="single-order-box__info">
                Адрес и дату доставки вашего заказа уточнит по&nbsp;телефону наш менеджер.
            </div>

            <? if ($order->comment) : ?>
                <div class="single-order_msg_adding">Дополнительные пожелания:<br/> «<?= $order->comment ?>»</div>
            <? endif ?>

            <div class="single-order-box__info">
                <? if ($order->isPaid()) : ?>
                    Заказ оплачен
                <? else : ?>
                    Вы сможете оплатить заказ при получении.
                <? endif ?>
            </div>
        <? endif ?>
    </div>
</div>

<? }; return $f;