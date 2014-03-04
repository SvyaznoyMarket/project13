<?php
/**
 * @var $page            \View\DefaultLayout
 * @var $user            \Session\User
 * @var $enterpizeCoupon \Model\EnterprizeCoupon\Entity
 * @var $error           string
 * @var $message         string
 */
?>

<? $data = \App::session()->get(\App::config()->enterprize['formDataSessionKey'], []) ?>

<div class="titleForm">Подтверди номер мобильного</div>

<? if ($error): ?>
    <p class="red"><?= $error ?></p>
<? endif ?>
<? if ($message): ?>
    <p class="green"><?= $message ?></p>
<? endif ?>

<div class="enterprizeConfirm">
    <p class="textConfirm"><strong><?= isset($data['mobile']) ? $data['mobile'] : '' ?><?//= preg_replace('/(\d{1,3})(\d{1,3})(\d{1,2})(\d{1,2})/i', '+7 ($1) $2-$3-$4', $userEntity->getEntity()) // должен быть формат +7 999 777-11-22 ?></strong></p>

    <form class="confirmForm" action="<?= $page->url('enterprize.confirmPhone.check') ?>" method="post">
        <label class="labelCode">Введите код</label>
        <input type="text" class="text" name="code" />

        <input class="confirmCode bigbutton" type="submit" value="Подтвердить" />
    </form>

    <form class="confirmForm" action="<?= $page->url('enterprize.confirmPhone.create') ?>" method="post">
        <input type="hidden" name="isRepeatRending" value="true" />

        <input type="submit" class="newCode mBtnGrey" value="Новый код" />
    </form>
</div>