<?php
/**
 * @var $page     \View\User\LoginPage
 * @var $form     \View\User\LoginForm|\View\User\RegistrationForm|null
 */
?>

<div class="bPageLogin clearfix">
    <div class="bPageLogin_eLogo fl"><a href="/"></a></div>

    <div class="popup popup-auth" data-state="default" id="auth-block" style="display: block">
        <div class="authWrap">
            <?= $page->render('user/_login-form') ?>
            <?= $page->render('user/_register-form') ?>
        </div>

        <?= $page->render('user/_reset-form') ?>
    </div>
</div>