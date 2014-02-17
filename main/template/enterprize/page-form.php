<?php
/**
 * @var $page             \View\DefaultLayout
 * @var $user             \Session\User
 * @var $form             \View\Enterprize\Form
 */
?>

<div>
    <h1>Заполни три поля, и ты в игре!</h1>

    <form class="formDefault" action="<?= $page->url('enterprize.form.update') ?>" method="post">
        <input type="hidden" name="user[enterprize_coupon]" value="<?= $form->getEnterprizeCoupon() ?>" />

        <fieldset class="formDefault__fields">
            <label class="formDefault__label">Имя:</label>
            <input class="formDefault__inputText" type="text" name="user[name]" value="<?= $form->getName() ?>" />

            <label class="formDefault__label">Мобильный телефон:</label>
            <input class="formDefault__inputText" type="text" name="user[phone]" value="<?= $form->getPhone() ?>" />

            <label class="formDefault__label">E-mail:</label>
            <input class="formDefault__inputText" type="text" name="user[email]" value="<?= $form->getEmail() ?>" />

            <ul class="bInputList">
                <li class="bInputList__eListItem ">
                    <input class="jsCustomRadio bCustomInput mCustomCheckBig" id="subscribe" name="user[subscribe]" type="checkbox" />
                    <label class="bCustomLabel mCustomLabelBig mChecked" for="subscribe">Получить рекламную рассылку</label>
                </li>

                <li class="bInputList__eListItem ">
                    <input class="jsCustomRadio bCustomInput mCustomCheckBig" name="user[agree]" id="agree" type="checkbox" />
                    <label class="bCustomLabel mCustomLabelBig" for="agree">Согласен с <a style="text-decoration: underline;" href="#">условиями оферты</a></label>
                </li>
            </ul>

            <input class="formDefault__btnSubmit mBtnOrange" type="submit" value="Играть!" />
        </fieldset>
    </form>
</div>