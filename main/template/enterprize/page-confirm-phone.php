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
    <p class="textConfirm">Код для подтверждения выслан на мобильный <strong><?= isset($data['mobile']) ? $data['mobile'] : '' ?><?//= preg_replace('/(\d{1,3})(\d{1,3})(\d{1,2})(\d{1,2})/i', '+7 ($1) $2-$3-$4', $userEntity->getEntity()) // должен быть формат +7 999 777-11-22 ?></strong></p>
    <?=$page->render('enterprize/form-confirm-phone')?>
</div>