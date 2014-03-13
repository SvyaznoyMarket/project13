<?php
/**
 * @var $page \View\Layout
 */
?>

<noindex>
<!-- Auth -->
<div class="popup" class="enterprizeAuthBox" id="enterprize-auth-block">
    <i title="Закрыть" class="close">Закрыть</i>

    <div class="enterprizeAuthBox__title"></div>

    <div class="bFormLogin enterprizeAuthBox__form">
        <?= $page->render('form-forgot') ?>
        <?= $page->render('form-login') ?>
    </div>
</div>
<!-- /Auth -->
</noindex>