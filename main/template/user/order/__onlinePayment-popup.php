<?php

/**
 * @param \Helper\TemplateHelper $helper
 * @param \Model\Order\Entity $order
 * @param \Model\PaymentMethod\PaymentEntity $paymentEntity
 */
$f = function(
    \Helper\TemplateHelper $helper,
    \Model\Order\Entity $order,
    \Model\PaymentMethod\PaymentEntity $paymentEntity
) {

$formUrl = \App::router()->generate('orderV3.paymentForm');
?>

<div class="payments-popup js-payment-popup">
    <div class="js-payment-popup-closer payments-popup__closer"></div>

    <div class="orderPayment_msg_head">
        Онлайн-оплата
    </div>
    <div class="order-payment__sum-msg">
    <?
        $sum = $paymentEntity ? ($paymentEntity->getPaymentSumByMethodId($order->paymentId)) : null;
        if (!$sum) {
            $sum = $order->paySum;
        }

        /** @var \Model\PaymentMethod\PaymentMethod\PaymentMethodEntity[] $paymentMethods */
        $onlinePaymentMethods = array_filter($paymentEntity->methods, function(\Model\PaymentMethod\PaymentMethod\PaymentMethodEntity $paymentMethod) {
            return $paymentMethod->isOnline;
        });
        $paymentMethodsByDiscount = [];
        foreach ($onlinePaymentMethods as $iPaymentMethod) {
            $index = $iPaymentMethod->discount ? 0 : 1;
            $paymentMethodsByDiscount[$index][] = $iPaymentMethod;
        }
        ksort($paymentMethodsByDiscount);

        $discountContainerId = sprintf('id-onlineDiscount-container', $order->id);
        $sumContainerId = sprintf('id-onlineDiscountSum-container', $order->id);
    ?>
        К оплате <span class="order-payment__sum"><span class="<?= $sumContainerId ?>"><?= $helper->formatPrice($sum) ?></span> <span class="rubl">p</span></span>
    </div>

    <? foreach ($paymentMethodsByDiscount as $discountIndex => $paymentMethodChunk): ?>
        <ul class="payment-methods__lst <? if (0 === $discountIndex): ?>payment-methods__lst_discount<? endif ?>">
            <? foreach ($paymentMethodChunk as $paymentMethod): ?>
            <?
                /** @var \Model\PaymentMethod\PaymentMethod\PaymentMethodEntity|null $paymentMethod */
                $containerId = sprintf('id-order-%s-paymentMethod-container', $order->id);
                $elementId = sprintf('order_%s-paymentMethod_%s', $order->id, $paymentMethod->id);
                $checked = $order->paymentId == $paymentMethod->id;
            ?>
                <li class="payment-methods__i">
                    <input
                        id="<?= $elementId ?>"
                        type="radio"
                        name="<?= sprintf('paymentMethodId_%s', $order->id) ?>"
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
                            'formContainer'     => '.' . $containerId,
                            'discountContainer' => '.' . $discountContainerId,
                            'sumContainer'      => '.' . $sumContainerId,
                        ]) ?>"
                        class="customInput customInput-defradio2 js-customInput js-order-onlinePaymentMethod"
                        <? if ($checked): ?>
                            checked="checked"
                            data-checked="true"
                        <? endif ?>
                        />
                    <label for="<?= $elementId ?>" class="customLabel customLabel-defradio2<? if ($checked): ?> mChecked<? endif ?>">
                        <?= $paymentMethod->name ?>
                        <? if ($image = $paymentMethod->icon): ?>
                            <img class="payment-methods__img" src="<?= $image ?>" alt="<?= $helper->escape($paymentMethod->name) ?>" />
                        <? endif ?>
                    </label>
                </li>
            <? endforeach ?>
        </ul>
        <? if ((0 === $discountIndex) && isset($paymentMethodChunk[0]->discount['value'])): ?>
            <div class="payment-methods__discount discount">
                <div class="<?= $discountContainerId ?>">
                    <span class="discount__val">Скидка <?= $paymentMethodChunk[0]->discount['value'] ?>%</span>
                </div>
            </div>
        <? endif ?>
    <? endforeach ?>
    <div class="payments-popup__pay <?= $containerId ?>"></div>
    <p class="orderPayment_msg_hint">Вы будете перенаправлены на сайт платежной системы.</p>
</div>

<? }; return $f;