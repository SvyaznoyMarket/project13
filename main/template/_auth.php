<?php
/**
 * @var $page         \View\Layout
 * @var $oauthEnabled array
 * @var $form         \EnterApplication\Form\LoginForm
 */

if (!isset($form)) $form = new \EnterApplication\Form\LoginForm();
$req = \App::request();
$redirect_to = $req->getPathInfo();
// SITE-4576 Пустая фраза поиска при входе в личный кабинет
if ($req->routeName == 'search') $redirect_to .= '?' . $req->getQueryString();

if (!isset($showRegisterForm)) $showRegisterForm = true;
?>

<noindex>
    <!-- Registration -->
    <div class="popup popup-auth js-login-content" data-state="default" id="auth-block">
        <span class="close close-auth">Закрыть</span>
        
        <div class="authWrap">
            <?= $page->render('user/_login-form', ['redirect_to' => $redirect_to]) ?>
            <?= $page->render('user/_register-form') ?>
        </div>

        <?= $page->render('user/_reset-form') ?>
    </div>
    <!-- /Registration -->
</noindex>