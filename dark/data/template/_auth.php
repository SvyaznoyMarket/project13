<?php
/**
 * @var $page \View\Layout
 */
?>

<!-- Registration -->
<div class="popup" id="auth-block">
    <i title="Закрыть" class="close">Закрыть</i>
	<h2 class="pouptitle">Вход в Enter</h2>

    <div class="registerbox">
        <?= $page->render('form-forgot') ?>
        <?= $page->render('form-login') ?>
        <?= $page->render('form-register') ?>

        <div class="clear"></div>
    </div>
</div>
<!-- /Registration -->
