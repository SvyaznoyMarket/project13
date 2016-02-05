<?php
return function(
    \Helper\TemplateHelper $helper,
    \Model\OrderDelivery\Entity\Order $order
) {

    if ($order->seller && !$order->seller->isEnter() && !$order->seller->isSvyaznoy() && !$order->seller->isSordex()) {
        return;
    }

    $couponErrors = array_filter($order->errors, function( \Model\OrderDelivery\Error $error) { return isset($error->details['coupon_number']); });
    $couponNumber = $couponErrors ? $couponErrors[0]->details['coupon_number'] : null;
    ?>

    <div class="orderCol_f_l">
        <span class="orderCol_f_t brb-dt jsShowDiscountForm" style="display: <?= $couponNumber !== null ? 'none' : 'inline-block' ?>">Ввести код скидки</span>
    </div>

    <div class="orderCol_f_l" style="display: <?= $couponNumber === null ? 'none' : 'block' ?>">
        <div class="orderCol_f_t">Код скидки<? if (\App::config()->order['checkCertificate']): ?>, подарочный сертификат<? endif ?></div>

        <input class="cuponField textfieldgrey" type="text" name="" value="<?= $couponNumber ?>" />

        <div class="cuponPin" style="display: none">
            <label class="cuponLbl">PIN:</label>
            <input class="cuponField cuponPin_it textfieldgrey jsCertificatePinInput" type="text" name="" value="" />
        </div>

        <? foreach ($couponErrors as $err) : ?>
            <? if ($err->code == 404) : ?>
                <div class="cuponErr">Скидки с таким кодом не существует</div>
            <? elseif ($err->code == 1001) : ?>
                <div class="cuponErr">Купон неприменим к данному заказу</div>
            <? elseif ($err->code == 1022) : ?>
                <div class="cuponErr">Купон уже был использован или истек срок действия</div>
            <? else : ?>
                <div class="cuponErr"><?= $err->message ?></div>
            <? endif ?>
        <? endforeach ?>

        <div><button class="cuponBtn btnLightGrey jsApplyDiscount">Применить</button></div>
    </div>

<? } ?>