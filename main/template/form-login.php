<?php
/**
 * @var $page     \View\Layout
 * @var $request  \Http\Request
 * @var $form     \View\User\LoginForm
 */
?>

<?php
if (!isset($form)) $form = new \View\User\LoginForm();
?>

<form action="<?= $page->url('user.login') ?>" class="form bFormLogin__ePlace jsLoginForm" method="post">
    <legend class="bFormLogin__ePlaceTitle">У меня есть логин и пароль</legend>

    <label class="bFormLogin__eLabel">E-mail или мобильный телефон:</label>
    <input class="text bFormLogin__eInput jsSigninUsername" type="text" value="<?= $form->getUsername() ?>" name="signin[username]" />

    <label class="bFormLogin__eLabel">Пароль:</label>
    <a class="bFormLogin__eLinkHint mForgotPassword jsForgotPwdTrigger" href="javascript:void(0)">Забыли пароль?</a>

    <input class="text bFormLogin__eInput jsSigninPassword" type="password" name="signin[password]" />

    <input type="submit" class="bigbutton bFormLogin__eBtnSubmit jsSubmit" data-loading-value="Вхожу..." value="Войти" />
</form>