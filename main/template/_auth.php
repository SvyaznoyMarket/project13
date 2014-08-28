<?php
/**
 * @var $page \View\Layout
 */
?>

<noindex>
<!-- Registration -->
<div class="popup" id="auth-block">
    <i title="Закрыть" class="close">Закрыть</i>

    <div class="bPopupTitle">Вход в Enter</div>

    <div class="bFormLogin">
        <?= $page->render('form-forgot') ?>
        <?= $page->render('form-login') ?>
        <?= $page->render('form-register') ?>
    </div>
    <a href="<?= $page->url('user.login.external', ['providerName' => 'vkontakte' ]) ?>" >Войти через VK</a>
    <a href="<?= $page->url('user.login.external', ['providerName' => 'facebook' ]) ?>" >Войти через FB</a>
</div>
<!-- /Registration -->
</noindex>