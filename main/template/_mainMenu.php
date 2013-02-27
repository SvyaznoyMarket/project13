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
    <li class="bCorner"></li>
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

    <li class="bMainMenuLevel-<?= $level ?>__eItem clearfix <? if ($class) echo ' ' . $class ?>">
        <? if ($iMenu->getLink()): ?>
            <a class="bMainMenuLevel-<?= $level ?>__eLink" href="<?= $iMenu->getLink() ?>">
                <span class="bMainMenuLevel-<?= $level ?>__eIcon"></span>
                <span class="bMainMenuLevel-<?= $level ?>__eTitle"><?= $iMenu->getName() ?></span>
            </a>
        <? elseif ($iMenu->getName()): ?>
                <p class="bMainMenuLevel-<?= $level ?>__eLink">
                    <span class="bMainMenuLevel-<?= $level ?>__eIcon"></span>
                    <span class="bMainMenuLevel-<?= $level ?>__eTitle"><?= $iMenu->getName() ?></span>
                </p>
        <? endif ?>

        <? if ((bool)$iMenu->getChild() && ($level <= 3)): ?>
            <?= $page->render('_mainMenu', ['menu' => $iMenu->getChild(), 'level' => $level + 1]) ?>
        <? endif ?>
    </li>
<? $i++; endforeach ?>
</ul>