<?php

return function(
    \Helper\TemplateHelper $helper,
    array $menu,
    \Model\Menu\Entity $parent = null,
    $catalogJsonBulk = [],
    $promoHtmlBulk = [],
    $level = 1
) {

/**
 * @var $menu   \Model\Menu\Entity[]
 * @var $iMenu  \Model\Menu\Entity
 */

$count = count($menu);
?>

<? if (1 == $level): ?>
    <style type="text/css">
        <? $count = count($menu); $i = 1; foreach ($menu as $iMenu): ?>
        .mId<?= $i ?> .bMainMenuLevel-1__eIcon {
        <?= $iMenu->css ?>
        }
        .mId<?= $i ?>:hover .bMainMenuLevel-1__eIcon{
        <?= $iMenu->cssHover ?>
        }
        <? if ($iMenu->color): ?>
        .mId<?= $i ?> .bMainMenuLevel-1__eTitle{
            color: <?=$iMenu->color ?>;
        }
        <? endif ?>
        <? if ($iMenu->colorHover): ?>
        .mId<?= $i ?>:hover .bMainMenuLevel-1__eTitle{
            color: <?=$iMenu->colorHover ?>;
        }
        <? endif ?>

        <? if ($i == $count): ?>

        <? $j = 1; foreach ($iMenu->child as $child): ?>
        .mId<?= $iMenu->priority . '-' . $j ?> .bMainMenuLevel-2__eIcon {
        <?= $child->css ?>
        }
        .mId<?= $iMenu->priority . '-' . $j ?>:hover .bMainMenuLevel-2__eIcon {
        <?= $child->cssHover ?>
        }
        <? $j++; endforeach ?>
        <? endif ?>
        <? $i++; endforeach ?>
    </style>
<? endif ?>

<? if($level == 3 && $parent instanceof \Model\Menu\Entity) { ?>
    <? $parentToken = preg_replace('/.*\//', '', $parent->link) ?>
    <? $showPromo = (!empty($parentToken) && !empty($promoHtmlBulk[$parentToken]['menu_promo']) && !empty($catalogJsonBulk[$parentToken]['use_promo_in_menu'])) ?>
<? } ?>

<ul class="bMainMenuLevel-<?= $level ?><?= empty($showPromo) ? '' : ' noPadding' ?>">
    <? if(!empty($showPromo)) { ?>
        <?= $promoHtmlBulk[$parentToken]['menu_promo'] ?>
    <? } else { ?>
        <? if ((3 ==$level) && $parent instanceof \Model\Menu\Entity && $parent->image): ?>
            <li class="bMainMenuLevel-<?= $level ?>__eHead"><?= $parent->name ?></li>
            <li class="bMainMenuLevel-<?= $level ?>__eImageItem"><img class="bMainMenuLevel-<?= $level ?>__eImage" width="150" src="<?= $parent->image ?>" alt="<?= $helper->escape($parent->name) ?>" /></li>
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
                <?
                $token = preg_replace('/.*\//', '', $iMenu->link);
                $showImage = !empty($catalogJsonBulk[$token]) && !empty($catalogJsonBulk[$token]['logo_path']) && !empty($catalogJsonBulk[$token]['use_logo']);
                ?>
                <? if ($iMenu->link): ?>
                    <a class="bMainMenuLevel-<?= $level ?>__eLink" href="<?= $iMenu->link ?>">
                        <span class="bMainMenuLevel-<?= $level ?>__eIcon">&nbsp;<?//= 0  === strpos($iMenu->getImage(), '&') ? $iMenu->getImage() : '' ?></span>
                        <span class="bMainMenuLevel-<?= $level ?>__eTitle">
                        <? if ($showImage): ?>
                            <img src="<?= $catalogJsonBulk[$token]['logo_path'] ?>">
                        <? else: ?>
                            <?= $iMenu->name ?>
                        <? endif ?>
                        </span>
                        <div class="bCorner"></div>
                    </a>
                <? elseif ($iMenu->name): ?>
                    <div class="bMainMenuLevel-<?= $level ?>__eLink">
                        <span class="bMainMenuLevel-<?= $level ?>__eIcon"></span>
                        <span class="bMainMenuLevel-<?= $level ?>__eTitle">
                        <? if ($showImage): ?>
                                <img src="<?= $catalogJsonBulk[$token]['logo_path'] ?>">
                            <? else: ?>
                                <?= $iMenu->name ?>
                            <? endif ?>
                        </span>
                        <div class="bCorner"></div>
                    </div>
                <? endif ?>

                <? if ($level <= 2): ?>
                    <?= $helper->render('__mainMenu', ['menu' => $iMenu->child, 'level' => $level + 1, 'parent' => $iMenu, 'catalogJsonBulk' => $catalogJsonBulk, 'promoHtmlBulk' => $promoHtmlBulk]) ?>
                <? endif ?>
            </li>
        <? $i++; endforeach ?>
    <? } ?>
</ul>

<? };
