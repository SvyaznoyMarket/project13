<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\BasicEntity $product
) {

    if (!(\App::config()->product['lowerPriceNotification'] && $product->getMainCategory() && $product->getMainCategory()->getPriceChangeTriggerEnabled())) {
        return '';
    }

    $user = \App::user();

    $price = ($product->getMainCategory() && $product->getMainCategory()->getPriceChangePercentTrigger())
        ? round($product->getPrice() * $product->getMainCategory()->getPriceChangePercentTrigger())
        : 0;

    $uEmail = $user->getEntity() ? $user->getEntity()->getEmail() : null;
?>

    <div class="priceSale">
        <span class="dotted jsLowPriceNotifer">Узнать о снижении цены</span>
        <div class="bLowPriceNotiferPopup popup">
            <i class="close"></i>
            <div class="uEntered">
                <? if (empty($uEmail)): ?>
                    <div class="bLowPriceNotiferPopup__eTitle">
                        Для этого укажите, пожалуйста, Ваш емейл в <a href="/private" title="Перейти в личный кабинет">личном кабинете</a>.
                    </div>
                <? else: ?>
                    <div class="bLowPriceNotiferPopup__eTitle">
                        Вы получите письмо,<br/>когда цена станет ниже
                        <? if ($price && ($price < $product->getPrice())): ?>
                            <strong class="price"><?= $helper->formatPrice($price) ?></strong> <span class="rubl">p</span>
                        <? endif ?>
                    </div>
                    <input class="bLowPriceNotiferPopup__eInputEmail" placeholder="Ваш email" value="<?= $user->getEntity() ? $user->getEntity()->getEmail() : '' ?>" />
                    <p class="bLowPriceNotiferPopup__eError red"></p>
                    <a href="#" class="bLowPriceNotiferPopup__eSubmitEmail button bigbuttonlink mDisabled" data-url="<?= $helper->url('product.notification.lowerPrice', ['productId' => $product->getId()]) ?>">Сохранить</a>
                <? endif ?>
            </div>
        </div>
    </div>

<? };