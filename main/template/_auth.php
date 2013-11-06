<?php
/**
 * @var $page \View\Layout
 */
?>

<!-- Registration -->
<div class="popup" id="auth-block">
    <i title="Закрыть" class="close">Закрыть</i>

    <div class="bPopupTitle">ВХОД В ENTER</div>

    <div class="bFormLogin">
        <?= $page->render('form-forgot') ?>
        <?= $page->render('form-login') ?>
        <?= $page->render('form-register') ?>
    </div>
</div>
<!-- /Registration -->