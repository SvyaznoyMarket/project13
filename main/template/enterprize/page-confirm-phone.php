<?php
/**
 * @var $page   \View\DefaultLayout
 * @var $user   \Session\User
 * @var $error string
 */

$userEntity = $user->getEntity();
if (!$userEntity) return;
?>

<div class="titleForm">Подтверди номер мобильного</div>

<? if ($error): ?>
    <p class="red"><?= $error ?></p>
<? endif ?>

<div class="enterprizeConfirm">
    <p class="textConfirm">Мы отправили смс с кодом подтверждения на номер <strong><?= $userEntity->getMobilePhone() ?><?//= preg_replace('/(\d{1,3})(\d{1,3})(\d{1,2})(\d{1,2})/i', '+7 ($1) $2-$3-$4', $userEntity->getEntity()) // должен быть формат +7 999 777-11-22 ?></strong></p>

    <form action="<?= $page->url('enterprize.confirmPhone.check') ?>" method="post">
        <label class="labelCode">Введите код</label>

        <input type="text" class="text" name="code" />

        <input class="newCode mBtnGrey" type="button" value="Новый код" />

        <input class="confirmCode bigbutton" type="submit" value="Подтвердить" />
    </form>
</div>