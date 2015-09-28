<?php

/**
 * @param \Helper\TemplateHelper $helper
 * @param \Model\Order\Entity $order
 * @param \Model\PaymentMethod\PaymentEntity|null $orderPayment
 * @return string
 */
$f = function(
    \Helper\TemplateHelper $helper,
    \Model\Order\Entity $order,
    \Model\PaymentMethod\PaymentEntity $orderPayment = null
) {
    if (!$orderPayment || !$orderPayment->methods) {
        return '';
    }

    /** @var \Model\PaymentMethod\PaymentMethod\PaymentMethodEntity[] $paymentMethods */
    $paymentMethods = array_filter($orderPayment->methods, function(\Model\PaymentMethod\PaymentMethod\PaymentMethodEntity $paymentMethod) {
        return $paymentMethod->isOnline;
    });

    $formUrl = \App::router()->generate('orderV3.paymentForm');
?>

    <div class="orderPayment orderPaymentWeb id-orderPaymentPreview-container jsOnlinePaymentPossible jsOnlinePaymentPossibleNoMotiv">
        <!-- Заголовок-->
        <!-- Блок в обводке -->
        <div class="orderPayment_block orderPayment_noOnline">

            <div class="orderPayment_msg orderPayment_noOnline_msg">
                <div class="orderPayment_msg_head">
                    Онлайн-оплата
                </div>
                <div class="order-payment__sum-msg">
                    К оплате <span class="order-payment__sum"><?= $helper->formatPrice($order->getSum()) ?> <span class="rubl">p</span></span>
                </div>
                <div class="orderPayment_msg_shop orderPayment_pay">
                    <ul class="orderPaymentWeb_lst-sm">
                    <? foreach ($paymentMethods as $paymentMethod): ?>
                        <li class="orderPaymentWeb_lst-sm-i"><a href="#"><img src="<?= $paymentMethod->icon ?>"></a></li>
                    <? endforeach ?>
                    </ul>
                    <button
                        class="orderPayment_btn btn3 js-showBlock"
                        data-show-block="<?= $helper->json([
                            'target'     => '.id-orderPaymentList-container',
                            'hideTarget' => '.id-orderPaymentPreview-container',
                        ]) ?>"
                    >Оплатить онлайн</button>
                </div>
                <p class="orderPayment_msg_hint">Вы будете перенаправлены на сайт платежной системы.</p>
            </div>
        </div>
    </div>

    <div class="orderPayment orderPaymentWeb id-orderPaymentList-container" style="display: none;">
        <!-- Заголовок-->
        <!-- Блок в обводке -->
        <div class="orderPayment_block orderPayment_noOnline">

            <div class="orderPayment_msg orderPayment_noOnline_msg">
                <div class="orderPayment_msg_head">
                    Онлайн-оплата
                </div>
                <div class="order-payment__sum-msg">
                    К оплате <span class="order-payment__sum"><?= $helper->formatPrice($order->getSum()) ?> <span class="rubl">p</span></span>
                </div>

                <!-- Этот блок идентичен блоку, который используется на 2м шаге, но у меня не получилось здесь отрендерить его-->
                <div class="payment-methods__discount discount">
                    <div class="id-onlineDiscount-container" style="display: none;">
                        <span class="discount__pay-type">Онлайн-оплата</span>
                        <span class="discount__val">Скидка 15%</span>
                    </div>
                </div>
                <ul class="payment-methods__lst">
                    <? foreach ($paymentMethods as $paymentMethod): ?>
                    <?
                        $elementId = sprintf('paymentMethod-%s', $paymentMethod->id);
                        $checked = $order->paymentId == $paymentMethod->id;
                    ?>
                    <li class="payment-methods__i">
                        <input
                            id="<?= $elementId ?>"
                            type="radio"
                            name="onlinePaymentMethodId"
                            value="<?= $paymentMethod->id ?>"
                            data-url="<?= $formUrl ?>"
                            data-value="<?= $helper->json([
                                'method' => $paymentMethod->id,
                                'order'  => $order->id,
                                'number' => $order->number,
                                'url'    => \App::router()->generate('orderV3.complete', ['context' => $order->context]),
                            ]) ?>"
                            <? if (in_array($paymentMethod->id, \App::config()->payment['discountIds'])): ?>
                                data-discount="true"
                            <? endif ?>
                            data-relation="<?= $helper->json([
                                'formContainer'     => '.id-paymentForm-container',
                                'discountContainer' => '.id-onlineDiscount-container',
                            ]) ?>"
                            class="customInput customInput-defradio2 js-customInput js-order-onlinePaymentMethod"
                            <? if ($checked): ?> checked="checked"<? endif ?>
                        />
                        <label for="<?= $elementId ?>" class="customLabel customLabel-defradio2<? if ($checked): ?> mChecked<? endif ?>">
                            <?= $paymentMethod->name ?>
                            <? if ($image = $paymentMethod->icon): ?>
                                <img class="payment-methods__img" src="<?= $image ?>" />
                            <? endif ?>
                        </label>
                    </li>
                <? endforeach ?>
                </ul>

                <div class="orderPayment_msg_shop orderPayment_pay id-paymentForm-container">
                    <!--<button class="orderPayment_btn btn3">Оплатить</button>-->
                </div>
                <p class="orderPayment_msg_hint">Вы будете перенаправлены на сайт платежной системы.</p>
            </div>
        </div>
    </div>

<? }; return $f;