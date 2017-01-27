<?php
/**
 */
return function (
    \Helper\TemplateHelper $helper,
    \Model\OrderDelivery\Entity\Order $order
) {

    if ($order->seller && !$order->seller->isEnter() && !$order->seller->isSvyaznoy() && !$order->seller->isSordex()) {
        return;
    }

    $couponErrors = array_filter($order->errors, function (\Model\OrderDelivery\Error $error) {
        return isset($error->details['coupon_number']);
    });
    $couponNumber = $couponErrors ? $couponErrors[0]->details['coupon_number'] : null;

    $inputSelectorId = 'id-discountInput-' . $order->block_name;
    ?>

    <? if (\App::config()->order['enableDiscountCodes']): ?>
        <div class="order-discount js-order-discount-container">

            <div class="order-discount__tl js-order-discount-opener"><span>Применить код скидки<? if (\App::config()->order['checkCertificate']): ?>, подарочный сертификат<? endif ?></span></div>

            <div class="order-discount__row <? if (!$couponErrors): ?>order-discount__row_hide<? endif ?> js-order-discount-content">
                <div class="order-discount__row-inner order-discount__row-inner_right">
                    <div class="order-ctrl <?= ($couponErrors ? 'error' : '') ?>">
                        <label class="order-ctrl__lbl">
                            <? foreach ($couponErrors as $err) : ?>
                                <? if ($err->code == 404) : ?>
                                    Скидки с таким кодом не существует
                                <? elseif ($err->code == 1001) : ?>
                                    Купон неприменим к данному заказу
                                <? elseif ($err->code == 1022) : ?>
                                    Купон уже был использован или истек срок действия
                                <? else : ?>
                                    <?= $err->message ?>
                                <? endif ?>
                            <? endforeach ?>
                        </label>
                        <input class="order-ctrl__input <?= $inputSelectorId ?>" value="<?= $couponNumber ?>">
                    </div>
                    <button
                        class="order-btn order-btn--default jsApplyDiscount-1509"
                        data-value="<?= $helper->json([
                            'block_name' => $order->block_name,
                        ]) ?>"
                        data-relation="<?= $helper->json([
                            'number' => '.' . $inputSelectorId,
                        ]) ?>"
                    >Применить
                    </button>
                </div>

                <div class="jsCertificatePinField order-discount__pin" style="display: none">
                    <div class="order-ctrl order-discount__pin-inn">
                        <label class="order-ctrl__lbl js-order-ctrl__lbl order-discount__pin-lbl">PIN:</label>
                        <input class="order-ctrl__input order-ctrl__input_float-label js-order-ctrl__input jsCertificatePinInput" type="text" name="" value="">
                    </div>
                </div>
            </div>
        </div>
    <? endif ?>
<? };