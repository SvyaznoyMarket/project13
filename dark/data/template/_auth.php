<?php
/**
 * @var $page \View\DefaultLayout
 */
?>

<!-- Registration -->
<div class="popup" id="auth-block">
    <i title="Закрыть" class="close">Закрыть</i>
    <div class="popupbox width694">
        <h2 class="pouptitle">Вход в Enter</h2>

        <div class="registerbox">
            <?= $page->render('form-forgot') ?>
            <?= $page->render('form-login') ?>
            <?= $page->render('form-register') ?>

            <div class="clear"></div>
        </div>

    </div>
</div>
<!-- /Registration -->
