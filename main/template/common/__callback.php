<?php

/**
 * @param \Helper\TemplateHelper $helper
 * @param string|null $view Внешний вид: телефонная трубка, колокольчик, ...
 */
$f = function(
    \Helper\TemplateHelper $helper,
    $view = null,
    $callbackPhrases = []
) {
    if (null === $view) {
        $view = 'phone';
    }

    $userEntity = \App::user()->getEntity();
    $phone = $userEntity ? preg_replace('/^8/', '', $userEntity->getMobilePhone()) : '';
?>

<div class="call-back-btn js-callback-button">
    <? if (true): ?><div class="call-back-btn__alert">!</div><? endif ?>

    <? if ('phone' === $view): ?>
        <img src="/styles/callback/img/phone.png" alt="">
        <? elseif ('bell' === $view): ?>
        <img src="/styles/callback/img/bell.png" alt="" style="top: 56%;">
    <? endif ?>
    <div class="call-back-btn__inner-txt" style="display:none;">Нужна помощь</div>
    <div class="call-back-btn__txt js-callback-hint-field" data-hint="<?= $helper->json($callbackPhrases) ?>">Помогу найти</div>
</div>

<div class="js-callback-popup callback-popup">
    <div class="callback-popup__content js-callback-popup-content">
        <a class="js-callback-popup-close callback-popup__close" href="#">&times;</a>
        <div class="callback-popup__cell">
            <? if ('phone' === $view): ?>
                <img src="/styles/callback/img/phone-b.png" alt="">
            <? elseif ('bell' === $view): ?>
                <img src="/styles/callback/img/bell-b.png" alt="">
            <? endif ?>
        </div>

        <div class="callback-popup__cell callback-popup__cell_right">
            <div class="callback-popup__title">
                <span>Оставьте номер телефона<br>и мы вам перезвоним</span>
            </div>
            <form class="js-callback-form" action="<?= $helper->url('user.callback.create') ?>" method="post">
                <div class="callback-popup__field js-callback-field">
                    <div class="callback-popup__error js-callback-error"></div>
                    <input class="callback-popup__phone js-callback-input js-callback-phone" type="text" name="user[phone]" data-field="phone" value="<?= $phone ?>" >
                </div>

                <fieldset class="callback-popup__btn-block">
                    <input class="callback-popup__btn js-callback-submit" type="submit" value="Перезвонить мне">
                </fieldset>
            </form>
        </div>
    </div>
</div>

<? }; return $f;