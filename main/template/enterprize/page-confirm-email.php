<?php
/**
 * @var $page            \View\DefaultLayout
 * @var $enterpizeCoupon \Model\EnterprizeCoupon\Entity
 * @var $error           string
 * @var $message         string
 */
?>

<? $data = \App::session()->get(\App::config()->enterprize['formDataSessionKey'], []) ?>

<? if ($error): ?>
    <p class="red enterprizeWar"><?= $error ?></p>
<? endif ?>
<? if ($message): ?>
    <p class="green enterprizeWar"><?= $message ?></p>
<? endif ?>

<div class="enterprizeConfirm">
    <?=$page->render('enterprize/form-confirm-email')?>
</div>