<?php
/**
 * @var $page             \View\DefaultLayout
 * @var $form             \View\Enterprize\Form
 * @var $authSource       string|null
 */
if (!isset($authSource))     $authSource = null;
if (!isset($form)) {
    $form = new \View\Enterprize\Form();
}
?>

<form class="formDefault jsEnterprizeForm" action="<?= \App::router()->generate($form->getRoute()) ?>" method="post">
    <input type="hidden" name="user[guid]" value="<?= $form->getEnterprizeCoupon() ?>" />

    <fieldset class="formDefault__fields">
        <label class="formDefault__label">Имя:</label>
        <input class="formDefault__inputText jsName" type="text" name="user[name]" value="<?= $form->getName() ?>" />

        <label class="formDefault__label">Мобильный телефон:</label>
        <input class="formDefault__inputText jsMobile formDefault__inputText--styled" type="text" name="user[mobile]" placeholder="8 (___) ___-__-__" data-mask="8 (xxx) xxx-xx-xx" value="<?= $form->getMobile() ?>" <? if ('phone' === $authSource): ?>readonly="readonly"<? endif ?> />

        <label class="formDefault__label">E-mail:</label>
        <input class="formDefault__inputText jsEmail" type="text" name="user[email]" value="<?= $form->getEmail() ?>" <? if ('email' === $authSource): ?>readonly="readonly"<? endif ?> />

        <ul class="bInputList mEnterPrizeSubscr">
            <li class="bInputList__eListItem ">
                <input type="hidden" name="user[isSubscribe]" value="1" />
                <input class="jsCustomRadio bCustomInput mCustomCheckBig jsSubscribe" id="isSubscribe" type="checkbox" checked="checked" disabled="disabled" />
                <label class="bCustomLabel mCustomLabelBig mChecked" for="subscribe">Получить рекламную рассылку</label>
            </li>

            <li class="bInputList__eListItem ">
                <input class="jsCustomRadio bCustomInput mCustomCheckBig jsAgree" name="user[agree]" id="agree" type="checkbox" <? if ($form->getAgree()): ?>checked="checked"<? endif ?> />
                <label class="bCustomLabel mCustomLabelBig<? if ($form->getAgree()): ?> mChecked<? endif ?>" for="agree">Согласен с <a style="text-decoration: underline;" href="/reklamnaya-akcia-enterprize" target="_blank">условиями оферты</a></label>
            </li>
        </ul>

        <input class="formDefault__btnSubmit mBtnOrange" type="submit" value="<?=$form->getSubmit()?>" />
    </fieldset>
</form>