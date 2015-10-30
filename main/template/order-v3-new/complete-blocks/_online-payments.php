<?php

/**
 * @param \Helper\TemplateHelper $helper
 * @param \Model\Order\Entity $order
 * @param \Model\PaymentMethod\PaymentEntity|null $orderPayment
 * @param string $title
 * @return string
 */
$f = function(
    \Helper\TemplateHelper $helper,
    \Model\Order\Entity $order,
    \Model\PaymentMethod\PaymentEntity $orderPayment = null,
    $title = 'Онлайн-оплата'
) {
    if (!$orderPayment || !$orderPayment->methods) {
        return '';
    }

    /** @var \Model\PaymentMethod\PaymentMethod\PaymentMethodEntity[] $paymentMethods */
    $paymentMethods = array_filter($orderPayment->methods, function(\Model\PaymentMethod\PaymentMethod\PaymentMethodEntity $paymentMethod) {
        return $paymentMethod->isOnline;
    });

    // SITE-6304
    $checkedPaymentMethodId = $order->paymentId;
    if (!array_key_exists($order->paymentId, $paymentMethods) && ($paymentMethod = reset($paymentMethods) ?: null)) {
        /** @var \Model\PaymentMethod\PaymentMethod\PaymentMethodEntity $paymentMethod */
        $checkedPaymentMethodId = $paymentMethod->id;
    }

    $paymentMethodsByDiscount = [];
    foreach ($paymentMethods as $paymentMethod) {
        $index = $paymentMethod->discount ? 0 : 1;
        $paymentMethodsByDiscount[$index][] = $paymentMethod;
    }
    ksort($paymentMethodsByDiscount);

    $formUrl = \App::router()->generate('orderV3.paymentForm');
?>

    <div class="orderPayment orderPaymentWeb id-orderPaymentPreview-container jsOnlinePaymentPossible jsOnlinePaymentPossibleNoMotiv">
        <!-- Заголовок-->
        <!-- Блок в обводке -->
        <div class="orderPayment_block orderPayment_noOnline">

            <div class="orderPayment_msg orderPayment_noOnline_msg">
                <div class="orderPayment_msg_head">
                    <?= $title ?>
                </div>
                <div class="order-payment__sum-msg">
                <?
                    $sum = ($checkedPaymentMethodId && $orderPayment) ? ($orderPayment->getPaymentSumByMethodId($checkedPaymentMethodId)) : null;
                    if (!$sum) {
                        $sum = $order->paySum;
                    }
                ?>
                    К оплате <span class="order-payment__sum"><?= $helper->formatPrice($sum) ?> <span class="rubl">p</span></span>
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
                    <?= $title ?>
                </div>
                <div class="order-payment__sum-msg">
                    К оплате <span class="order-payment__sum"><span class="id-onlineSum-container"><?= $helper->formatPrice($sum) ?></span> <span class="rubl">p</span></span>
                </div>

                <? foreach ($paymentMethodsByDiscount as $discountIndex => $paymentMethods): ?>
                <ul class="payment-methods__lst <? if (0 === $discountIndex): ?>payment-methods__lst_discount<? endif ?>">
                    <? foreach ($paymentMethods as $paymentMethod): ?>
                    <?
                        $elementId = sprintf('order_%s-paymentMethod_%s', $order->id, $paymentMethod->id);
                        $checked = $checkedPaymentMethodId == $paymentMethod->id;
                    ?>
                    <li class="payment-methods__i">
                        <input
                            id="<?= $elementId ?>"
                            type="radio"
                            name="onlinePaymentMethodId"
                            value="<?= $paymentMethod->id ?>"
                            data-url="<?= $formUrl ?>"
                            data-value="<?= $helper->json([
                                'action' => $paymentMethod->getOnlineDiscountAction() ?: null,
                                'method' => $paymentMethod->id,
                                'order'  => $order->id,
                                'number' => $order->number,
                                'url'    => \App::router()->generate('orderV3.complete', ['context' => $order->context], true),
                            ]) ?>"
                            <? if ($sum = ($paymentMethod->getOnlineDiscountActionSum() ?: $order->paySum)): ?>
                                data-sum="<?= $helper->json([
                                    'name'  => isset($paymentMethod->discount['value']) ? ('Скидка ' . $paymentMethod->discount['value'] .'%') : null,
                                    'value' => $helper->formatPrice($sum)
                                ])?>"
                            <? endif ?>
                            data-relation="<?= $helper->json([
                                'formContainer'     => '.id-paymentForm-container',
                                'discountContainer' => '.id-onlineDiscount-container',
                                'sumContainer'      => '.id-onlineSum-container',
                            ]) ?>"
                            class="customInput customInput-defradio2 js-customInput js-order-onlinePaymentMethod"
                            <? if ($checked): ?>
                                data-checked="true"
                                checked="checked"
                            <? endif ?>
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

                    <? if (0 === $discountIndex): ?>
                    <div class="payment-methods__discount discount">
                        <div class="id-onlineDiscount-container">
                            <span class="discount__pay-type">Онлайн-оплата</span>
                            <span class="discount__val">Скидка 15%</span>
                        </div>
                    </div>
                    <? endif ?>
                <? endforeach ?>

                <div class="orderPayment_msg_shop orderPayment_pay id-paymentForm-container">
                    <!--<button class="orderPayment_btn btn3">Оплатить</button>-->
                </div>
                <p class="orderPayment_msg_hint">Вы будете перенаправлены на сайт платежной системы.</p>
            </div>
        </div>
    </div>

<? }; return $f;