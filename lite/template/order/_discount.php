<?php
return function(
    \Helper\TemplateHelper $helper,
    \Model\OrderDelivery\Entity\Order $order
) {

    // не показываем поля дискаунта, если заказ партнерский (Связной - исключение)
    if ($order->isPartnerOffer() && !$order->seller->isSvyaznoy()) return;

    $couponErrors = array_filter($order->errors, function( \Model\OrderDelivery\Error $error) { return isset($error->details['coupon_number']); });
    $couponNumber = $couponErrors ? $couponErrors[0]->details['coupon_number'] : null;
    ?>

    <div class="order-summ__left">
        <span class="order-summ_t dotted jsShowDiscountForm" style="display: <?= $couponNumber !== null ? 'none' : 'inline-block' ?>">Ввести код скидки</span>
    </div>

    <div class="order-summ__left" style="display: <?= $couponNumber === null ? 'none' : 'block' ?>">
        <div class="order-summ_t">Код скидки, подарочный сертификат</div>

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