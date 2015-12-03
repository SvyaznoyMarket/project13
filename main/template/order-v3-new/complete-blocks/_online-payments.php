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
    $title = 'Оплатить онлайн со скидкой'
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

    $formUrl = \App::router()->generate('orderV3.paymentForm');

    $sum = ($checkedPaymentMethodId && $orderPayment) ? ($orderPayment->getPaymentSumByMethodId($checkedPaymentMethodId)) : null;
    if (empty($sum)) {
        $sum = $order->paySum;
    }

    $containerId = 'id-paymentForm-container';
    $sumContainerId = 'id-onlineSum-container';
?>

    <div class="orderPayment orderPayment--static orderPaymentWeb id-orderPaymentPreview-container">
        <!-- Заголовок-->
        <!-- Блок в обводке -->
        <div class="orderPayment_block orderPayment_noOnline">

            <div class="orderPayment_msg orderPayment_noOnline_msg">
                <div class="orderPayment_msg_head">
                    <?= $title ?>
                </div>
                <div class="order-payment__sum-msg">
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

    <div class="orderPayment orderPayment--static orderPaymentWeb id-orderPaymentList-container" style="display: none;">
        <!-- Заголовок-->
        <!-- Блок в обводке -->
        <div class="orderPayment_block orderPayment_noOnline">

            <div class="orderPayment_msg orderPayment_noOnline_msg">
                <div class="orderPayment_msg_head">
                    <?= $title ?>
                </div>
                <div class="order-payment__sum-msg">
                    К оплате <span class="order-payment__sum"><span class="<?= $sumContainerId ?>"><?= $helper->formatPrice($sum) ?></span> <span class="rubl">p</span></span>
                </div>

                <? foreach ((new \View\Partial\PaymentMethods())->execute($helper, $paymentMethods, $checkedPaymentMethodId)['paymentMethodGroups'] as $paymentMethodGroup): ?>
                    <ul class="payment-methods__lst <? if ($paymentMethodGroup['discount']): ?>payment-methods__lst_discount<? endif ?>">
                        <? foreach ($paymentMethodGroup['paymentMethodGroups'] as $paymentMethodGroup2): ?>
                            <? if (count($paymentMethodGroup2['paymentMethods']) == 1): ?>
                            <?
                                $elementId = sprintf('order_%s-paymentMethod_%s', $order->id, $paymentMethodGroup2['paymentMethods'][0]['id']);
                                $name = 'onlinePaymentMethodId';
                            ?>

                                <li class="payment-methods__i">
                                    <input
                                        id="<?= $elementId ?>"
                                        type="radio"
                                        name="<?= $name ?>"
                                        value="<?= $paymentMethodGroup2['paymentMethods'][0]['id'] ?>"
                                        data-url="<?= $formUrl ?>"
                                        data-value="<?= $helper->json([
                                            'action' => isset($paymentMethodGroup['discount']) ? $paymentMethodGroup['discount']['action'] : null,
                                            'method' => $paymentMethodGroup2['paymentMethods'][0]['id'],
                                            'order'  => $order->id,
                                            'number' => $order->number,
                                            'url'    => \App::router()->generate('orderV3.complete', ['context' => $order->context], true),
                                        ]) ?>"
                                        <? if ($sum = (empty($paymentMethodGroup['discount']['sum']) ? $order->paySum : $paymentMethodGroup['discount']['sum'])): ?>
                                            data-sum="<?= $helper->json([
                                                'value' => $helper->formatPrice($sum)
                                            ])?>"
                                        <? endif ?>
                                        data-relation="<?= $helper->json([
                                            'formContainer'     => '.' . $containerId,
                                            'sumContainer'      => '.' . $sumContainerId,
                                        ]) ?>"
                                        class="customInput customInput-defradio2 js-customInput js-order-onlinePaymentMethod"
                                        <? if ($paymentMethodGroup2['paymentMethods'][0]['selected']): ?>
                                            checked="checked"
                                            data-checked="true"
                                        <? endif ?>
                                        />
                                    <label for="<?= $elementId ?>" class="customLabel customLabel-defradio2<? if ($paymentMethodGroup2['paymentMethods'][0]['selected']): ?> mChecked<? endif ?>">
                                        <?= $helper->escape($paymentMethodGroup2['paymentMethods'][0]['name']) ?>
                                        <? if ($paymentMethodGroup2['paymentMethods'][0]['icon']): ?>
                                            <img class="payment-methods__img" src="<?= $helper->escape($paymentMethodGroup2['paymentMethods'][0]['icon']) ?>" alt="<?= $helper->escape($paymentMethodGroup2['paymentMethods'][0]['name']) ?>" />
                                        <? endif ?>
                                    </label>
                                </li>
                            <? endif ?>
                        <? endforeach ?>
                    </ul>

                    <?= $helper->renderWithMustache('order-v3-new/paymentMethod/discount', ['discount' => $paymentMethodGroup['discount']]) ?>
                <? endforeach ?>

                <div class="orderPayment_msg_shop orderPayment_pay <?= $containerId ?>">
                    <!--<button class="orderPayment_btn btn3">Оплатить</button>-->
                </div>
                <p class="orderPayment_msg_hint">Вы будете перенаправлены на сайт платежной системы.</p>
            </div>
        </div>
    </div>

<? }; return $f;