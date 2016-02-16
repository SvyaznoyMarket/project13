<?php
/**
 * @var $page \View\Layout
 */
?>

<div class="authForm authForm_reset js-resetForm">
    <span class="authForm_reset-close js-authForm_reset-close">Закрыть</span>
    <div class="authForm_reset-txt">
        <span>Новый пароль отправлен на </span>
        <span>mail@mail.ru</span>
    </div>
</div>


<form id="loginReset" class="authForm authForm_reset js-loginReset" action="<?= $page->url('user.forgot') ?>" method="post" style="display: ">
    <input type="text" class="authForm_it textfield js-inputReset" name="forgot[login]" value="" placeholder="Email или телефон">
</form>