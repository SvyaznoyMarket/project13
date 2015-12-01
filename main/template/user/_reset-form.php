<?php
/**
 * @var $page \View\Layout
 */
?>

<form class="authForm authForm_reset js-resetForm" action="<?= $page->url('user.forgot') ?>" method="post" style="/*display: none*/">
    <input type="hidden" name="forgot[gaClientId]" class="js-resetForm-gaClientId" />

    <!-- секция восстановления пароля -->
    <fieldset class="authForm_fld">
        <legend class="authForm_t legend">Восстановление пароля</legend>

        <!-- показываем при удачном восстановлении пароля, authForm_regbox скрываем -->
        <div class="js-message authForm_regcomplt"></div>
        <!--/ показываем при удачном восстановлении пароля, authForm_regbox скрываем -->

        <div class="authForm_regbox">
            <input type="text" class="authForm_it textfield" name="forgot[login]" value="" placeholder="Email или телефон">

            <input type="submit" class="authForm_is btnsubmit" name="" value="Отправить">
        </div>
    </fieldset>
    <!--/ секция восстановления пароля -->
</form>