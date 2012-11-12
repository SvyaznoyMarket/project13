<?php
/**
 * @var $page \View\Layout
 * @var $user \Session\User
 */
?>

<?
$userEntity = $user->getEntity();
?>

<? if (false && $user->getPhoto()): ?>
<div class="pb10">
    <span class="avatar">
        <b></b>
        <img src="<?= $userEntity->getPhoto() ?>" alt="" width="54" height="54"/>
    </span>
</div>
<? endif ?>

<div class="font16 pb5">
    Привет,<br/><strong><?= $userEntity->getName() ?></strong>
</div>

<div class="pb10">
    <?= $userEntity->getEmail()?><br/><? echo $userEntity->getMobilePhone() ?>
</div>

<div class="pb20">
    <? if ($userEntity->getBirthday()): ?>
        Дата рождения:<br/><?= $userEntity->getBirthday()->format('d.m.Y') ?>
        <br/>
    <? endif ?>

    <? if ($userEntity->getOccupation()): ?>
        Деятельность:<br/><?= $userEntity->getOccupation() ?>
    <? endif ?>
</div>
