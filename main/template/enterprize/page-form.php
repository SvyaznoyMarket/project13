<?php
/**
 * @var $page             \View\DefaultLayout
 * @var $form             \View\Enterprize\Form
 * @var $enterpizeCoupon  \Model\EnterprizeCoupon\Entity
 * @var $errors           array
 * @var $authSource       string|null
 */
?>

<div class="titleForm">Заполните три поля, и Вы &#8212; в игре!</div>

<? if (is_array($errors)): ?>
    <? foreach ($errors as $error): ?>
        <p class="red enterprizeWar"><?= $error ?></p>
    <? endforeach ?>
<? endif ?>

<form class="formDefault jsEnterprizeForm" action="<?= $page->url('enterprize.form.update') ?>" method="post">
    <input type="hidden" name="user[guid]" value="<?= $form->getEnterprizeCoupon() ?>" />

    <fieldset class="formDefault__fields">
 
            <label class="formDefault__label">Имя:</label>
            <input class="formDefault__inputText jsName" type="text" name="user[name]" value="<?= $form->getName() ?>" />

            <label class="formDefault__label">Мобильный телефон:</label>
            <input class="formDefault__inputText jsMobile" type="text" name="user[mobile]" value="<?= $form->getMobile() ?>" <? if ('phone' === $authSource): ?>readonly="readonly"<? endif ?> />

            <label class="formDefault__label">E-mail:</label>
            <input class="formDefault__inputText jsEmail" type="text" name="user[email]" value="<?= $form->getEmail() ?>" <? if ('email' === $authSource): ?>readonly="readonly"<? endif ?> />
   

        <ul class="bInputList mEnterPrizeSubscr">
            <li class="bInputList__eListItem ">
                <input class="jsCustomRadio bCustomInput mCustomCheckBig jsSubscribe" id="subscribe" name="user[subscribe]" type="checkbox" checked="checked" />
                <label class="bCustomLabel mCustomLabelBig mChecked" for="subscribe">Получить рекламную рассылку</label>
            </li>

            <li class="bInputList__eListItem ">
                <input class="jsCustomRadio bCustomInput mCustomCheckBig jsAgree" name="user[agree]" id="agree" type="checkbox" <? if($form->getAgree()): ?>checked="checked"<? endif ?> />
                <label class="bCustomLabel mCustomLabelBig<? if($form->getAgree()): ?> mChecked<? endif ?>" for="agree">Согласен с <a style="text-decoration: underline;" href="/reklamnaya-akcia-enterprize" target="_blank">условиями оферты</a></label>
            </li>
        </ul>

        <input class="formDefault__btnSubmit mBtnOrange" type="submit" value="Играть!" />
    </fieldset>
</form>