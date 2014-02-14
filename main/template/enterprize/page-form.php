<?php
/**
 * @var $page             \View\DefaultLayout
 * @var $user             \Session\User
 * @var $form             \View\Enterprize\Form
 */
?>

<div>
    <h1>заполни три поля, и ты в игре!</h1>
    <div class="clear"></div>

    <div>
        <form action="<?= $page->url('enterprize.form.update') ?>" method="post">
            <input type="hidden" name="user[enterprize_coupon]" value="<?= $form->getEnterprizeCoupon() ?>" />

            <fieldset>
                <label>Имя:</label>
                <div><input type="text" name="user[name]" value="<?= $form->getName() ?>" /></div>

                <label>Мобильный телефон:</label>
                <div><input type="text" name="user[phone]" value="<?= $form->getPhone() ?>" /></div>

                <label>E-mail:</label>
                <div><input type="text" name="user[email]" value="<?= $form->getEmail() ?>" /></div>


                <div>
                    <input id="subscribe" name="user[subscribe]" type="checkbox" />
                    <label for="subscribe">Получить рекламную рассылку</label>
                </div>

                <div>
                    <input name="user[agree]" id="agree" type="checkbox" />
                    <label for="agree">Согласен с <a href="#">условиями оферты</a></label>
                </div>

                <input type="submit" value="Играть!" />
            </fieldset>
        </form>
    </div>
</div>