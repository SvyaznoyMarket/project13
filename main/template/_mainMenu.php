<?php
/**
 * @var $page  \View\Layout
 * @var $menu  \Model\Menu\Entity[]
 * @var $level int
 */
?>

<?
$level = isset($level) ? $level : 1;
?>

<ul class="bMainMenuLevel-<?= $level ?>">
<? foreach ($menu as $iMenu): ?>
    <li <li class="bMainMenuLevel-<?= $level ?>__eItem clearfix <? if (\Model\Menu\Entity::ACTION_SEPARATOR === $iMenu->getAction()): ?> bMainMenuLevel-<?= $level ?>__eSeparator<? endif ?>">
        <? if ($iMenu->getLink()): ?>
            <a class="bMainMenuLevel-<?= $level ?>__eTitle" href="<?= $iMenu->getLink() ?>"><?= $iMenu->getName() ?></a>
        <? elseif ($iMenu->getName()): ?>
            <span class="bMainMenuLevel-<?= $level ?>__eTitle"><?= $iMenu->getName() ?></span>
        <? endif ?>

        <? if ((bool)$iMenu->getChild()): ?>
            <?= $page->render('_mainMenu', ['menu' => $iMenu->getChild(), 'level' => $level + 1]) ?>
        <? endif ?>
    </li>
<? endforeach ?>
</ul>
