<?php
/**
 * @var $page  \View\Layout
 * @var $menu  \Model\Menu\Entity[]
 * @var $level int
 */
?>

<?
$level = isset($level) ? $level : 1;
$count = count($menu);
?>

<ul class="bMainMenuLevel-<?= $level ?>">
<? $i = 1; foreach ($menu as $iMenu): ?>
<?
    $class = '';
    if (\Model\Menu\Entity::ACTION_SEPARATOR === $iMenu->getAction()) {
        $class .= ' bMainMenuLevel-' . $level . '__eSeparator';
    }
    if ((1 == $level) && ($i == $count)) {
        $class .= ' mMore';
    }
    $class = trim($class);
?>

    <li class="bMainMenuLevel-<?= $level ?>__eItem<? if ($class) echo ' ' . $class ?>">
        <? if ($iMenu->getLink()): ?>
            <a class="bMainMenuLevel-<?= $level ?>__eTitle" href="<?= $iMenu->getLink() ?>"><?= $iMenu->getName() ?></a>
        <? elseif ($iMenu->getName()): ?>
            <span class="bMainMenuLevel-<?= $level ?>__eTitle"><?= $iMenu->getName() ?></span>
        <? endif ?>

        <? if ((bool)$iMenu->getChild()): ?>
            <?= $page->render('_mainMenu', ['menu' => $iMenu->getChild(), 'level' => $level + 1]) ?>
        <? endif ?>
    </li>
<? $i++; endforeach ?>
</ul>