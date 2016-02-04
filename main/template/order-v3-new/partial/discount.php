<?php
/**
 * @param \Model\EnterprizeCoupon\Entity[] $userEnterprizeCoupons
 */
return function (
    \Helper\TemplateHelper $helper,
    \Model\OrderDelivery\Entity\Order $order,
    array $userEnterprizeCoupons = []
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
    <div class="order-discount js-order-discount-container">

        <span class="order-discount__tl js-order-discount-opener">Применить код скидки/фишки, подарочный сертификат</span>

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

            <? if ($userEnterprizeCoupons): ?>
                <div class="order-discount__row-inner">
                    <div class="order-discount__row-cell">
                        <img src="/styles/order-new/img/i-ep.png" alt="i-ep">
                    </div>

                    <div class="order-discount__row-cell">
                        <div class="order-ctrl__custom-select order-discount__select js-order-discount-enterprize-container">
                            <span class="order-ctrl__custom-select-item_title js-order-discount-enterprize-opener">
                                Выбрать фишку
                            </span>

                            <ul class="order-ctrl__custom-select-list js-order-discount-enterprize-content">
                                <? foreach ($userEnterprizeCoupons as $userEnterprizeCoupon): ?>
                                    <li class="order-ctrl__custom-select-item js-order-discount-enterprize-item" data-block_name="<?= $helper->escape($order->block_name) ?>" data-coupon-number="<?= $helper->escape($userEnterprizeCoupon->getDiscount() ? $userEnterprizeCoupon->getDiscount()->getNumber() : '') ?>">
                                        <div class="order-discount__select-img-block">
                                            <span class="ep-coupon order-discount__ep-coupon-img" style="background-image: url(<?= $helper->escape($userEnterprizeCoupon->getBackgroundImage()) ?>);">
                                                <? if ($userEnterprizeCoupon->getImage()): ?>
                                                    <span class="ep-coupon__ico order-discount__ep-coupon-icon">
                                                        <img src="<?= $helper->escape($userEnterprizeCoupon->getImage()) ?>">
                                                    </span>
                                                <? endif ?>
                                            </span>
                                        </div>
                                        <div class="order-discount__ep-coupon-txt">
                                            <span class="order-discount__ep-coupon-txt-desc">
                                                Скидка <?= $helper->formatPrice($userEnterprizeCoupon->getPrice()) . ($userEnterprizeCoupon->getIsCurrency() ? ' <span class="rubl">p</span>' : '%') ?> на <?= $helper->escape($helper->lcfirst($userEnterprizeCoupon->getName())) ?>
                                            </span>
                                            <span class="order-discount__ep-coupon-txt-total">
                                                минимальная сумма заказа <?= $helper->formatPrice($userEnterprizeCoupon->getMinOrderSum()) ?> <span class="rubl">p</span>
                                            </span>
                                        </div>
                                    </li>
                                <? endforeach ?>
                            </ul>
                        </div>
                    </div>
                </div>
            <? endif ?>
        </div>
    </div>

<? };