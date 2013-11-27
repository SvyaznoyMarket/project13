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
<div class="userAvatar">
    <img src="<?= $userEntity->getPhoto() ?>" alt="" width="54" height="54"/>
</div>
<? endif ?>

<div class="userName">
    Привет,<strong class="userName__name"><?= $userEntity->getName() ?></strong>
</div>

<ul class="uderInfo">
    <li class="uderInfo__item"><?= $userEntity->getEmail()?></li>

    <li class="uderInfo__item"><? echo $userEntity->getMobilePhone() ?></li>

    <li class="uderInfo__item">
        <? if ($userEntity->getBirthday()): ?>
            Дата рождения:<br/><?= $userEntity->getBirthday()->format('d.m.Y') ?>
        <? endif ?>
    </li>

    <li class="uderInfo__item">
        <? if ($userEntity->getOccupation()): ?>
            Деятельность:<br/><?= $userEntity->getOccupation() ?>
        <? endif ?>
    </li>
</ul>
