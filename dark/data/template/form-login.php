<?php
/**
 * @var $page \View\DefaultLayout
 * @var $form \View\User\LoginForm
 * @var $redirect string
 */
?>

<?php
if (empty($redirect)) $redirect = \App::request()->getRequestUri();
if (!isset($form)) $form = new \View\User\LoginForm();
?>

<form id="login-form" action="<?= $page->url('user.login') ?>" class="form" method="post">
    <input type="hidden" name="redirect_to" value="<?= $redirect ?>"/>

    <div class="fl width327 mr20">
        <div class="font16 pb20">У меня есть логин и пароль</div>

        <div class="pb5">E-mail или мобильный телефон:</div>
        <div class="pb5">
            <? if ($error = $form->getError('username')) echo $page->render('_formError', array('error' => $error)) ?>
            <input type="text" id="signin_username" class="text width315 mb10" value="<?= $form->getUsername() ?>" name="signin[username]"/>
        </div>

        <div class="pb5">
            <a id="forgot-pwd-trigger" href="<?= $page->url('user.forgot') ?>" class="fr orange underline">Забыли пароль?</a>
            Пароль:
        </div>
        <div class="pb5">
            <? if ($error = $form->getError('password')) echo $page->render('_formError', array('error' => $error)) ?>
            <input type="password" id="signin_password" class="text width315 mb10" name="signin[password]"/>
        </div>

        <input type="submit" class="fr button bigbutton" value="Войти" tabindex="4"/>
    </div>
</form>