<?php
/**
 * @var $page \View\Layout
 */
?>

<?php
$hasLoginLink = isset($hasLoginLink) ? $hasLoginLink : true;
?>

<form style="display: none;" action="<?= $page->url('user.forgot') ?>" class="form bFormLogin__ePlace jsResetPwdForm" method="post">
    <legend class="bFormLogin__ePlaceTitle">Восстановление пароля</legend>

    <label class="bFormLogin__eLabel jsForgotPwdLoginLabel">Введите e-mail или мобильный телефон, который использовали при регистрации, и мы пришлем вам пароль.</label>

    <div class="pb5">
        <input class="text bFormLogin__eInput jsForgotPwdLogin" type="text" value="" name="forgot[login]" />
    </div>

    <input type="submit" class="whitebutton bFormLogin__eBtnSubmit jsSubmit" data-loading-value="Идет обработка..." value="Отправить запрос" />

    <?php if ($hasLoginLink): ?>
        Если вы вспомнили пароль, то вам надо лишь<br/><strong><a href="javascript:void(0)" class="jsRememberPwdTrigger orange underline">войти в систему</a></strong>.
    <?php endif ?>
</form>