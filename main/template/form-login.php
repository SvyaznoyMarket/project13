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

<form action="<?= $page->url('user.login',['redirect_to'=>(isset($redirect_to)?$redirect_to:null)]) ?>" class="form bFormLogin__ePlace jsLoginForm" method="post">
    <fieldset class="bFormLogin__ePlace">
        <legend class="bFormLogin__ePlaceTitle">Мои логин и пароль</legend>

        <label class="bFormLogin__eLabel">E-mail или мобильный телефон:</label>
        <div><input class="text bFormLogin__eInput jsSigninUsername" type="text" value="<?= $form->getUsername() ?>" name="signin[username]" /></div>

        <label class="bFormLogin__eLabel">Пароль:</label>
        <a class="bFormLogin__eLinkHint mForgotPassword jsForgotPwdTrigger" href="javascript:void(0)">Забыли пароль?</a>

        <div><input class="text bFormLogin__eInput jsSigninPassword" type="password" name="signin[password]" /></div>

        <input type="submit" class="bigbutton bFormLogin__eBtnSubmit jsSubmit" data-loading-value="Вхожу..." value="Войти" />
    </fieldset>
</form>