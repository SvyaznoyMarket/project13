<? use \Model\PaymentMethod\PaymentMethod\PaymentMethodEntity;

$f  = function (
    \Helper\TemplateHelper $helper,
    \Model\Order\Entity $order
) {
    $deliveryText =
        !empty($order->deliveryDateInterval['name'])
        ? $order->deliveryDateInterval['name']
        : (
            $order->getDeliveredAt()
            ? $order->getDeliveredAt()->format('d.m.Y')
            : ''
        )
    ;
    if ($deliveryText && preg_match('/(день|дня|дней)$/', $deliveryText) && (false === strpos($deliveryText, 'егодня'))) {
        $deliveryText = 'через ' . $deliveryText;
    }
?>

<div class="orderPayment orderPayment--static <?= $order->isPaid() ? 'orderPaid jsOrderPaid': '' ?>">
    <div class="orderPayment_block orderPayment_block--border orderPayment_noOnline">

        <? if ((bool)$order->point) : ?>

            <div class="orderPayment_msg orderPayment_noOnline_msg">
                <div class="orderPayment_msg_head" style="text-align: left;">
                    <? if ($order->point->isEnterShop() || $order->point->isSvyaznoyShop() || $order->point->isEurosetPoint()) : ?>
                        Ждем вас <?= $deliveryText ?> в магазине
                    <? elseif ($order->point->isPickpoint()) : ?>
                        Вы можете забрать заказ из постамата <?= $deliveryText ?>
                    <? elseif ($order->point->isHermesPoint()) : ?>
                        Вы можете забрать заказ в пункте выдачи Hermes <?= $deliveryText ?>
                    <? endif ?>
                </div>
                <div class="orderPayment_msg_shop markerLst_row">

                    <? if ((bool)$order->point->subway) : ?>

                        <span class="markerList_col markerList_col-mark">
                            <i class="markColor" style="background-color: <?= $order->point->subway->getLine()->getColor() ?>"></i>
                        </span>
                        <span class="markerList_col">
                            <span class="orderPayment_msg_shop_metro"><?= $order->point->subway->getName() ?></span>

                    <? endif ?>

                        <span class="orderPayment_msg_shop_addr"><?= $order->point->address ?></span>
                            <? if ($order->shop) : ?>
                                <a href="<?= \App::router()->generateUrl('shop.show', ['pointToken' => $order->shop->getToken()])?>" class="orderPayment_msg_addr_link jsCompleteOrderShowShop" target="_blank">
                                    Как добраться
                                </a>
                            <? endif ?>
                    </span>

                    <? if (false && $order->comment) : ?>
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
            </div>

        <? else : ?>

            <div class="orderPayment_msg orderPayment_noOnline_msg">
                <div class="orderPayment_msg_head">
                    Время и место
                </div>
                <div class="orderPayment_msg_info info-phrase">
                    Адрес и дату доставки вашего заказа уточнит по&nbsp;телефону наш менеджер.
                </div>
                <? if (false && $order->comment) : ?>
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