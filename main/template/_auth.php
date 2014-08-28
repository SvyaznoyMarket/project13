<?php
/**
 * @var $page \View\Layout
 * @var $oauthEnabled array
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
    <? if ($oauthEnabled['vkontakte']): ?>
        <a href="<?= $page->url('user.login.external', ['providerName' => 'vkontakte' ]) ?>" >Войти через VK</a>
    <? endif; ?>
    <? if($oauthEnabled['facebook']): ?>
        <a href="<?= $page->url('user.login.external', ['providerName' => 'facebook' ]) ?>" >Войти через FB</a>
    <? endif; ?>
</div>
<!-- /Registration -->
</noindex>