<?php
/**
 * @var \View\User\LoginPage $page
 * @var \EnterApplication\Form\LoginForm|\EnterApplication\Form\RegisterForm|null $form
 */

if (!isset($redirect_to)) $redirect_to = null;

?>

<div class="bPageLogin clearfix">
    <div class="bPageLogin_eLogo fl"><a href="/"></a></div>

    <div class="popup popup-auth js-login-content" id="auth-block" style="display: block; float: left; margin: 20px 0 0 110px;">
        <div class="authWrap">
            <?= $page->render('user/_login-form', ['redirect_to' => $redirect_to, 'redirectUrlUserTokenParam' => $redirectUrlUserTokenParam]) ?>
            <?= $page->render('user/_register-form') ?>
        </div>

        <?= $page->render('user/_reset-form') ?>
    </div>
</div>