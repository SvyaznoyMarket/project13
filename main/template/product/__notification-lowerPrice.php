<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\BasicEntity $product,
    $isUserSubscribedToEmailActions,
    $actionChannelName
) {

    if (!(\App::config()->product['lowerPriceNotification'] && $product->getMainCategory() && $product->getMainCategory()->getPriceChangeTriggerEnabled())) {
        return '';
    }

    $user = \App::user();

    $price = ($product->getMainCategory() && $product->getMainCategory()->getPriceChangePercentTrigger())
        ? round($product->getPrice() * $product->getMainCategory()->getPriceChangePercentTrigger())
        : 0;

    $uEmail = $user->getEntity() ? $user->getEntity()->getEmail() : null;


    $id = 'priceNotifier-' . $product->getId();
?>

    <div class="priceSale">
        <span id="<?= $id ?>" class="dotted jsLowPriceNotifer">Узнать о снижении цены</span>
        <div class="bLowPriceNotiferPopup popup">
            <i class="close"></i>
            <div class="uEntered">
                <? if (empty($uEmail) && $user->getEntity()): ?>
                    <div class="bLowPriceNotiferPopup__eTitle">
                        Пожалуйста, укажите ваш e-mail в <a href="<?= $helper->url(\App::config()->user['defaultRoute']) ?>" title="Перейти в личный кабинет">личном кабинете</a>.
                    </div>
                <? else: ?>
                    <div class="bLowPriceNotiferPopup__eTitle">
                        Вы получите письмо,<br/>когда цена станет ниже
                        <? if ($price && ($price < $product->getPrice())): ?>
                            <strong class="price"><?= $helper->formatPrice($price) ?></strong> <span class="rubl">p</span>
                        <? endif ?>
                    </div>

                    <? if (!$isUserSubscribedToEmailActions): ?>
                        <label class="bLowPriceNotiferPopup__actionChannel clearfix checked">
                            <input type="checkbox" name="subscribe" value="1" autocomplete="off" class="bCustomInput subscribe jsSubscribe" checked="checked" />
                            <b></b><?= $helper->escape($actionChannelName) ?>
                        </label>
                    <? endif ?>

                    <input class="bLowPriceNotiferPopup__eInputEmail jsLowerPriceEmailInput" placeholder="Ваш email" value="<?= $user->getEntity() ? $user->getEntity()->getEmail() : '' ?>" />
                    <p class="bLowPriceNotiferPopup__eError red jsLowerPriceError"></p>
                    <a href="#" class="bLowPriceNotiferPopup__eSubmitEmail button bigbuttonlink mDisabled jsLowerPriceSubmitBtn" data-url="<?= $helper->url('product.notification.lowerPrice', ['productId' => $product->getId()]) ?>">Сохранить</a>
                <? endif ?>
            </div>
        </div>
    </div>

<? };