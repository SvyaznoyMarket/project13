<?php
/**
 * @var $page   \View\Layout
 * @var $menu   \Model\Menu\Entity[]
 * @var $parent \Model\Menu\Entity
 * @var $level  int
 */
?>

<?
if (!isset($parent)) $parent = null;
$level = isset($level) ? $level : 1;
$count = count($menu);
?>

<ul class="bMainMenuLevel-<?= $level ?>">

    <? if ((3 ==$level) && $parent instanceof \Model\Menu\Entity && $parent->getImage()): ?>
        <li class="bMainMenuLevel-<?= $level ?>__eHead"><?= $parent->getName() ?></li>
        <li class="bMainMenuLevel-<?= $level ?>__eImageItem"><img class="bMainMenuLevel-<?= $level ?>__eImage" width="150" src="<?= $parent->getImage() ?>" alt="<?= $page->escape($parent->getName()) ?>" /></li>
    <? endif ?>

<? $i = 1; foreach ($menu as $iMenu): ?>
<?
    $class = '';
    if (\Model\Menu\Entity::ACTION_SEPARATOR === $iMenu->getAction()) {
        $class .= ' bMainMenuLevel-' . $level . '__eSeparator';
    }
    if ((1 == $level) && ($i == $count)) {
        $class .= ' mMore';
    }
    if ((1 == $level) && (\Model\Menu\Entity::ACTION_PRODUCT_CATALOG !== $iMenu->getAction()) && (false === strpos($class, 'mMore'))) {
        $class .= ' mAction';
    }
    $class = trim($class);
?>

    <li class="bMainMenuLevel-<?= $level ?>__eItem clearfix <? if ($class) echo ' ' . $class ?>">
        <? if ($iMenu->getLink()): ?>
            <a class="bMainMenuLevel-<?= $level ?>__eLink" href="<?= $iMenu->getLink() ?>">
                <span class="bMainMenuLevel-<?= $level ?>__eIcon"><?= 0  === strpos($iMenu->getImage(), '&') ? $iMenu->getImage() : '' ?></span>
                <span class="bMainMenuLevel-<?= $level ?>__eTitle"><?= $iMenu->getName() ?></span>
                <div class="bCorner"></div>
            </a>
        <? elseif ($iMenu->getName()): ?>
            <div class="bMainMenuLevel-<?= $level ?>__eLink">
                <span class="bMainMenuLevel-<?= $level ?>__eIcon"></span>
                <span class="bMainMenuLevel-<?= $level ?>__eTitle"><?= $iMenu->getName() ?></span>
                <div class="bCorner"></div>
            </div>
        <? endif ?>

        <? if ($level <= 2): ?>
            <?= $page->render('_mainMenu', ['menu' => $iMenu->getChild(), 'level' => $level + 1, 'parent' => $iMenu]) ?>
        <? endif ?>
    </li>
<? $i++; endforeach ?>

</ul>