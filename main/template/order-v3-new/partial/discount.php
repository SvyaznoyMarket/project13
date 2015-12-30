<?php

return function (
    \Helper\TemplateHelper $helper,
    \Model\OrderDelivery\Entity\Order $order
) {

    // не показываем поля дискаунта, если заказ партнерский (Связной - исключение)
    if ($order->isPartnerOffer() && !$order->seller->isSvyaznoy()) return;

    $couponErrors = array_filter($order->errors, function (\Model\OrderDelivery\Error $error) {
        return isset($error->details['coupon_number']);
    });
    $couponNumber = $couponErrors ? $couponErrors[0]->details['coupon_number'] : null;

    $inputSelectorId = 'id-discountInput-' . $order->block_name;
    ?>
    <div class="order-discount">

        <span class="order-discount__tl js-discountToggle">Применить код скидки/фишки, подарочный сертификат</span>

        <div class="order-discount__row order-discount__row_hide js-discountBlock">
            <div class="order-discount__row-inner">
                <div class="order-ctrl <?= ($couponErrors ? 'error' : '') ?>">
                    <input class="order-ctrl__input <?= $inputSelectorId ?>" value="<?= $couponNumber ?>">
                    <label class="order-ctrl__lbl nohide">
                        <? foreach ($couponErrors as $err) : ?>
                            <? if ($err->code == 404) : ?>
                                Скидки с таким кодом<br>не существует
                            <? elseif ($err->code == 1001) : ?>
                                Купон неприменим<br>к данному заказу
                            <? elseif ($err->code == 1022) : ?>
                                Купон уже был использован<br>или истек срок действия
                            <? else : ?>
                                <?= $err->message ?>
                            <? endif ?>
                        <? endforeach ?>
                    </label>
                </div>

                <div class="order-discount__pin" style="display: none">
                    <div class="order-ctrl">
                        <label class="order-ctrl__lbl js-order-ctrl__lbl">PIN:</label>
                        <input class="order-ctrl__input order-ctrl__input_float-label js-order-ctrl__input jsCertificatePinInput" type="text" name="" value="" placeholder="PIN">
                    </div>
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

            <div class="order-discount__row-inner">
                <div class="order-discount__row-cell">
                    <img src="/styles/order-new/img/i-ep.png" alt="i-ep">
                </div>

                <div class="order-discount__row-cell">
                    <div class="order-ctrl__custom-select order-discount__select">
                        <span class="order-ctrl__custom-select-item_title">
                            Выберать фишку
                        </span>

                        <ul class="order-ctrl__custom-select-list">
                            <li class="order-ctrl__custom-select-item" data-value="1">
                                <div class="order-discount__select-img-block">
                                    <span class="ep-coupon order-discount__ep-coupon-img" style="background-image: url(http://content.enter.ru/wp-content/uploads/2014/03/fishka_orange_b1.png);">
                                                <span class="ep-coupon__ico order-discount__ep-coupon-icon">
                                                    <img
                                                        src="http://scms.enter.ru/uploads/media/e1/d7/a8/61389c42d60a432bd426ad08a0306fe0ca638ff7.png">
                                                </span>
                                    </span>
                                </div>
                                <div class="order-discount__ep-coupon-txt">
                                    <span class="order-discount__ep-coupon-txt-desc">
                                        Скидка 25% на бытовую технику
                                    </span>
                                    <span class="order-discount__ep-coupon-txt-total">
                                        минимальная сумма заказа 1000₽
                                    </span>
                                </div>
                            </li>

                            <li class="order-ctrl__custom-select-item">
                                <div class="order-discount__select-img-block">
                                    <span class="ep-coupon order-discount__ep-coupon-img" style="background-image: url(http://content.enter.ru/wp-content/uploads/2014/03/fishka_orange_b1.png);">
                                                <span class="ep-coupon__ico order-discount__ep-coupon-icon">
                                                    <img
                                                        src="http://scms.enter.ru/uploads/media/e1/d7/a8/61389c42d60a432bd426ad08a0306fe0ca638ff7.png">
                                                </span>
                                    </span>
                                </div>
                                <div class="order-discount__ep-coupon-txt">
                                    <span class="order-discount__ep-coupon-txt-desc">
                                        Скидка 25% на бытовую технику
                                    </span>
                                    <span class="order-discount__ep-coupon-txt-total">
                                        минимальная сумма заказа 1000₽
                                    </span>
                                </div>
                            </li>

                            <li class="order-ctrl__custom-select-item">
                                <div class="order-discount__select-img-block">
                                    <span class="ep-coupon order-discount__ep-coupon-img" style="background-image: url(http://content.enter.ru/wp-content/uploads/2014/03/fishka_orange_b1.png);">
                                                <span class="ep-coupon__ico order-discount__ep-coupon-icon">
                                                    <img
                                                        src="http://scms.enter.ru/uploads/media/e1/d7/a8/61389c42d60a432bd426ad08a0306fe0ca638ff7.png">
                                                </span>
                                    </span>
                                </div>
                                <div class="order-discount__ep-coupon-txt">
                                    <span class="order-discount__ep-coupon-txt-desc">
                                        Скидка 25% на бытовую технику
                                    </span>
                                    <span class="order-discount__ep-coupon-txt-total">
                                        минимальная сумма заказа 1000₽
                                    </span>
                                </div>
                            </li>

                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

<? };