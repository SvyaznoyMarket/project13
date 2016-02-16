<?php
/**
 * @var $page         \View\Layout
 * @var $oauthEnabled array
 * @var $form         \View\User\LoginForm
 */

if (!isset($form)) $form = new \View\User\LoginForm();
$req = \App::request();
$redirect_to = $req->getPathInfo();
// SITE-4576 Пустая фраза поиска при входе в личный кабинет
if ($req->attributes->get('route') == 'search') $redirect_to .= '?' . $req->getQueryString();

if (!isset($showRegisterForm)) $showRegisterForm = true;
?>

<noindex>
    <!-- Registration -->
    <div class="popup popup-auth js-login-content" data-state="default" id="auth-block">
        <span class="close close-auth">Закрыть</span>
        
        <div class="authWrap 123">
            <?= $page->render('user/_login-form', ['redirect_to' => $redirect_to]) ?>
            <?= $page->render('user/_register-form') ?>
        </div>

        <?= $page->render('user/_reset-form') ?>

        <!-- показываем этот текст в окне регистрации -->
        <!-- div class="authAct"><span class="brb-dt">Вход в Enter</span></div-->

        <!-- показываем этот текст в окне восстановления пароля -->
        <!-- <div class="authAct">Вспомнили? <span class="brb-dt">Вход в Enter</span></div> -->
    </div>
    <!-- /Registration -->
</noindex>