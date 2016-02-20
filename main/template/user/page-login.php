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
            <?= $page->render('user/_login-form', ['redirect_to' => $redirect_to]) ?>
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