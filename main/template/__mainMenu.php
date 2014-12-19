<?php

/**
 * @param \Helper\TemplateHelper $helper
 * @param array $menu
 * @param \Model\Menu\Entity|\Model\Menu\BasicMenuEntity $parent
 * @param int $level
 */
$f = function(
    \Helper\TemplateHelper $helper,
    array $menu,
    $parent = null,
    $level = 1
) {

/**
 * @var $menu   \Model\Menu\Entity[]
 * @var $iMenu  \Model\Menu\Entity
 */

$count = count($menu);
?>

<? if (2 == $level): // SITE-3862 ?><!--noindex--><? endif ?>

<ul class="bMainMenuLevel-<?= $level ?>">
    <? if ((3 == $level) && $parent instanceof \Model\Menu\Entity && $parent->image): ?>
        <li class="bMainMenuLevel-<?= $level ?>__eHead"><?= $parent->name ?></li>
        <li class="bMainMenuLevel-<?= $level ?>__eImageItem">
            <img class="bMainMenuLevel-<?= $level ?>__eImage lazyMenuImg" width="150" data-src="<?= $parent->image ?>" alt="<?= $helper->escape($parent->name) ?>" />
            <img class="bMainMenuLevel-<?= $level ?>__eImage" width="150" src="<?= $parent->image ?>" alt="<?= $helper->escape($parent->name) ?>" />
        </li>
        </noscript>

       <li class="bMainMenuLevel-<?= $level ?>__eProd">
            <div class="menuItem">
                <div class="menuItem_t">Товар дня!</div>
                <a class="menuItem_cnt" href="">
                    <img src="http://fs05.enter.ru/1/1/200/1c/236998.jpg" alt="" class="menuItem_img">
                    <span class="menuItem_n">Мобильный телефон Explay A240 черный</span>
                </a>

                <div class="menuItem_pr">22 290 <span class="rubl">p</span></div>
            </div>
       </li>
    <? endif ?>

    <? $i = 1; foreach ($menu as $iMenu): ?>
        <?
        $class = '';
        if (\Model\Menu\Entity::ACTION_SEPARATOR === $iMenu->action) {
            $class .= ' bMainMenuLevel-' . $level . '__eSeparator';
        }
        if ((1 == $level) && !$iMenu->action) {
            $class .= ' mMore';
        }
        if ((1 == $level) && (\Model\Menu\Entity::ACTION_PRODUCT_CATALOG !== $iMenu->action) && (false === strpos($class, 'mMore'))) {
            $class .= ' mAction';
        }
        if ((1 == $level) && (($count - $i) < 4) && (($count - $i) > 1)) {
            $class .= ' mMenuLeft';
        }
        if ((1 == $level) && empty($iMenu->child) && (\Model\Menu\Entity::ACTION_LINK == $iMenu->action)) {
            $class .= ' jsEmptyChild';
        }

        $class = trim($class);
        ?>

        <li class="bMainMenuLevel-<?= $level ?>__eItem clearfix mId<?= ($parent ? ($parent->priority . '-') : '') . $i ?> <? if ($class) echo ' ' . $class ?>">
            <? if ($iMenu->link): ?>
                <a class="bMainMenuLevel-<?= $level ?>__eLink" href="<?= $iMenu->link ?>">
                    <span class="bMainMenuLevel-<?= $level ?>__eIcon">&nbsp;<?//= 0  === strpos($iMenu->getImage(), '&') ? $iMenu->getImage() : '' ?></span>
                    <span class="bMainMenuLevel-<?= $level ?>__eTitle">
                    <? if ($iMenu->useLogo && $iMenu->logoPath != null): ?>
                        <img src="<?= $iMenu->logoPath ?>">
                    <? else: ?>
                        <? if ($iMenu->smallImage): ?>
                            <img src="<?= $iMenu->smallImage ?>" alt="<?= $iMenu->name ?>" />
                        <? else: ?>
                            <?= $iMenu->name ?>
                        <? endif; ?>
                    <? endif ?>
                    </span>
                    <div class="bCorner"></div>
                </a>
            <? elseif ($iMenu->name): ?>
                <div class="bMainMenuLevel-<?= $level ?>__eLink">
                    <span class="bMainMenuLevel-<?= $level ?>__eIcon"></span>
                    <? if ($iMenu->char): ?>
                        <span class="bMainMenuLevel-<?= $level ?>__eChar"><?= $iMenu->char ?></span>
                    <? endif ?>
                    <span class="bMainMenuLevel-<?= $level ?>__eTitle">
                    <? if ($iMenu->useLogo && $iMenu->logoPath != null): ?>
                            <img src="<?= $iMenu->logoPath ?>">
                        <? else: ?>
                            <? if ($iMenu->smallImage): ?>
                                <img src="<?= $iMenu->smallImage ?>" alt="<?= $iMenu->name ?>" />
                            <? else: ?>
                                <?= $iMenu->name ?>
                            <? endif; ?>
                        <? endif ?>
                    </span>
                    <div class="bCorner"></div>
                </div>
            <? endif ?>

            <? if ($level <= 2): ?>
                <?= $helper->render('__mainMenu', ['menu' => $iMenu->child, 'level' => $level + 1, 'parent' => $iMenu]) ?>
            <? endif ?>
        </li>
    <? $i++; endforeach ?>
</ul>

<? if (2 == $level): // SITE-3862 ?><!--/noindex--><? endif ?>

<? }; return $f;
