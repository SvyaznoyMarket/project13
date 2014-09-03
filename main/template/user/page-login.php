<?php
/**
 * @var $page     \View\User\LoginPage
 * @var $form     \View\User\LoginForm|\View\User\RegistrationForm|null
 */
?>

<div class="bPageLogin clearfix">
    <div class="bPageLogin_eLogo fl"><a href="/"></a></div>

    <div class="popup popup-auth" data-state="default" id="auth-block" style="display: block; float: left; margin: 20px 0 0 110px;">
        <div class="authWrap">
            <?= $page->render('user/_login-form') ?>
            <?= $page->render('user/_register-form') ?>
        </div>

        <?= $page->render('user/_reset-form') ?>

        <!-- показываем этот текст в окне входа на сайт -->
        <div class="authAct">
            <span
                class="brb-dt authForm_registerLink js-link"
                data-value="<?= $page->json(['target' => '#auth-block', 'state' => 'register']) ?>"
            >Регистрация</span>
            <span
                class="brb-dt authForm_authLink js-link"
                data-value="<?= $page->json(['target' => '#auth-block', 'state' => 'default']) ?>"
            >Войти</span>
        </div>
    </div>
</div>