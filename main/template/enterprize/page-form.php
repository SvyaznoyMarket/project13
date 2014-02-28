<?php
/**
 * @var $page             \View\DefaultLayout
 * @var $user             \Session\User
 * @var $form             \View\Enterprize\Form
 */
?>


<div class="titleForm">Заполни три поля, и ты в игре!</div>

<form class="formDefault jsEnterprizeForm" action="<?= $page->url('enterprize.form.update') ?>" method="post">
    <input type="hidden" name="user[guid]" value="<?= $form->getEnterprizeCoupon() ?>" />

    <fieldset class="formDefault__fields">
        <div>
            <label class="formDefault__label">Имя:</label>
            <input class="formDefault__inputText jsName" type="text" name="user[name]" value="<?= $form->getName() ?>" />
        </div>

        <div>
            <label class="formDefault__label">Мобильный телефон:</label>
            <input class="formDefault__inputText jsMobile" type="text" name="user[mobile]" value="<?= $form->getMobile() ?>" />
        </div>

        <div>
            <label class="formDefault__label">E-mail:</label>
            <input class="formDefault__inputText jsEmail" type="text" name="user[email]" value="<?= $form->getEmail() ?>" />
        </div>

        <ul class="bInputList">
            <li class="bInputList__eListItem ">
                <input class="jsCustomRadio bCustomInput mCustomCheckBig jsSubscribe" id="subscribe" name="user[subscribe]" type="checkbox" checked="checked" />
                <label class="bCustomLabel mCustomLabelBig mChecked" for="subscribe">Получить рекламную рассылку</label>
            </li>

            <li class="bInputList__eListItem ">
                <input class="jsCustomRadio bCustomInput mCustomCheckBig jsAgree" name="user[agree]" id="agree" type="checkbox" />
                <label class="bCustomLabel mCustomLabelBig" for="agree">Согласен с <a style="text-decoration: underline;" href="#">условиями оферты</a></label>
            </li>
        </ul>

        <input class="formDefault__btnSubmit mBtnOrange" type="submit" value="Играть!" />
    </fieldset>
</form>