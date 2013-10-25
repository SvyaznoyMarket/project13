<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\BasicEntity $product
) {

    if (!(\App::config()->product['lowerPriceNotification'] && $product->getMainCategory() && $product->getMainCategory()->getPriceChangeTriggerEnabled())) {
        return '';
    }

    /*$user = \App::user();
    $userEntity = $user->getEntity();
    $uEmail = ( $userEntity instanceof \Model\User\Entity ) ? $userEntity->getEmail() : null;*/

    $price = ($product->getMainCategory() && $product->getMainCategory()->getPriceChangePercentTrigger())
        ? round($product->getPrice() * $product->getMainCategory()->getPriceChangePercentTrigger())
        : 0;

?>

    <div class="priceSale">
        <span class="dotted jsLowPriceNotifer">Узнать о снижении цены</span>
        <div class="bLowPriceNotiferPopup popup">
            <i class="close"></i>

            <div class="uNotEntered">
                <div class="bLowPriceNotiferPopup__eTitle">
                    <?= $helper->render('user/_uShouldEnter') ?>
                </div>
            </div>

            <div class="uEntered">

                <div class="uNotHaveMail">
                    <div class="bLowPriceNotiferPopup__eTitle">
                        <?= $helper->render('user/_uNotHaveMail') ?>
                    </div>
                </div>

                <div class="uHaveMail">

                    <div class="bLowPriceNotiferPopup__eTitle">
                        Вы получите письмо,<br/>когда цена станет ниже
                        <? if ($price && ($price < $product->getPrice())): ?>
                            <strong class="price"><?= $helper->formatPrice($price) ?></strong> <span class="rubl">p</span>
                        <? endif ?>
                    </div>
                    <input class="bLowPriceNotiferPopup__eInputEmail uEmail" placeholder="Ваш email" value="<? //= $uEmail ? : '' ?>"/>
                    <p class="bLowPriceNotiferPopup__eError red"></p>
                    <a href="#" class="bLowPriceNotiferPopup__eSubmitEmail button bigbuttonlink mDisabled"
                       data-url="<?= $helper->url('product.notification.lowerPrice', ['productId' => $product->getId()]) ?>">Сохранить</a>

                </div>

            </div>

        </div>
    </div>

<? };