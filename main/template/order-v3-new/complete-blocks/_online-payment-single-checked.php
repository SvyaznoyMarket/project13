<?php

/**
 * @param \Helper\TemplateHelper $helper
 * @param \Model\Order\Entity $order
 * @param \Model\PaymentMethod\PaymentEntity|null $orderPayment
 * @param bool $onlineRedirect
 * @return string
 */
$f = function(
    \Helper\TemplateHelper $helper,
    \Model\Order\Entity $order,
    \Model\PaymentMethod\PaymentEntity $orderPayment = null,
    $onlineRedirect = false // SITE-6641
) {
    /** @var \Model\PaymentMethod\PaymentMethod\PaymentMethodEntity|null $paymentMethod */
    $paymentMethod = null;
    foreach ($orderPayment->methods as $iPaymentMethod) {
        if ($iPaymentMethod->id == $order->paymentId) {
            $paymentMethod = $iPaymentMethod;
            break;
        }
    }
    if (!$paymentMethod) {
        return '';
    }

    $formUrl = \App::router()->generateUrl('orderV3.paymentForm');
?>

    <!-- блок когда была выбран конкретный способ оплаты -->
    <div class="orderPayment orderPayment--static orderPaymentWeb">
        <!-- Заголовок-->
        <!-- Блок в обводке -->
        <div class="orderPayment_block orderPayment_noOnline">

            <div class="orderPayment_msg orderPayment_noOnline_msg">
                <div class="orderPayment_msg_head">
                    Оплатить онлайн
                    <? if ($paymentMethod->discount): ?>
                        со скидкой
                    <? endif ?>
                </div>

                <div class="orderPayment_msg_shop orderPayment_pay">
                    <!-- Здесь выводим иконки побольше -->
                    <input
                        type="hidden"
                        name="onlinePaymentMethodId"
                        value="<?= $paymentMethod->id ?>"
                        data-url="<?= $formUrl ?>"
                        data-value="<?= $helper->json([
                            'action' => $paymentMethod->discount ? $paymentMethod->discount->action : null,
                            'method' => $paymentMethod->id,
                            'order'  => $order->id,
                            'number' => $order->number,
                            'url'    => \App::router()->generateUrl('orderV3.complete', ['context' => $order->context], true),
                        ]) ?>"
                        data-relation="<?= $helper->json([
                            'formContainer' => '.id-paymentForm-container',
                        ]) ?>"
                        data-checked="true"
                        class="js-order-onlinePaymentMethod"
                    />
                    <div class="order-payment__choosed">
                        <?= $paymentMethod->name ?>
                        <? if ($image = $paymentMethod->icon): ?>
                            <img src="<?= $image ?>" alt="<?= $helper->escape($paymentMethod->name) ?>" <? if ($paymentMethod->id == 22): ?>class="order-payment__robokassa"<? endif ?> />
                        <? endif ?>
                    </div>

                    <div class="orderPayment_msg_shop orderPayment_pay id-paymentForm-container" <? if ($onlineRedirect): ?>data-submit="on"<? endif ?>>
                        <!--<button class="orderPayment_btn btn3">Оплатить онлайн</button>-->
                    </div>
                </div>
                <p class="orderPayment_msg_hint js-order-payment-hint">Вы будете перенаправлены на сайт платежной системы.</p>
            </div>
        </div>
    </div>
    <!-- END блок когда была выбран конкретный способ оплаты -->

<? }; return $f;