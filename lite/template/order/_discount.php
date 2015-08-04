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
        <div class="order-disount__title jsShowDiscountForm" style="display: <?= $couponNumber !== null ? 'none' : 'block' ?>"><span class="dotted">Ввести код скидки</span></div>
    </div>

    <div class="order-summ__left order-disount" style="display: <?= $couponNumber === null ? 'none' : 'block' ?>">
        <div class="order-disount__title"><span class="dotted">Код скидки, подарочный сертификат</span></div>

        <input class="order-disount__it it" type="text" name="" value="<?= $couponNumber ?>" />

        <div class="order-disount-pin" style="display: none">
            <label class="order-disount-pin__label">PIN-код</label>
            <input class="order-disount-pin__it order-disount__it it jsCertificatePinInput" type="text" name="" value="" />
        </div>

        <? foreach ($couponErrors as $err) : ?>
            <? if ($err->code == 404) : ?>
                <div class="order-disount__error">Скидки с таким кодом не существует</div>
            <? elseif ($err->code == 1001) : ?>
                <div class="order-disount__error">Купон неприменим к данному заказу</div>
            <? elseif ($err->code == 1022) : ?>
                <div class="order-disount__error">Купон уже был использован или истек срок действия</div>
            <? else : ?>
                <div class="order-disount__error"><?= $err->message ?></div>
            <? endif ?>
        <? endforeach ?>

        <div><button class="order-disount__btn btn-apply jsApplyDiscount">Применить</button></div>
    </div>

<? } ?>