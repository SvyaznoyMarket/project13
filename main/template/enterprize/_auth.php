<?php
/**
 * @var $page \View\Layout
 */
?>

<noindex>
<!-- Auth -->
<div class="popup" id="enterprize-auth-block">
    <i title="Закрыть" class="close">Закрыть</i>

    <div class="bPopupTitle">ВХОД В ENTER</div>

    <div class="bFormLogin">
        <?= $page->render('form-forgot') ?>
        <?= $page->render('form-login') ?>
    </div>
</div>
<!-- /Auth -->
</noindex>