<?php

return function(
    \Helper\TemplateHelper $helper,
    array $categories,
    $catalogConfig = [],
    \Model\Product\Category\Entity $currentCategory = null,
    \Model\Product\Category\TreeEntity $rootCategoryInMenu = null
) {
    /** @var $categories \Model\Product\Category\Entity[] */


?>

    <!-- TCHIBO - слайдер-меню разделов Чибо -->
    <div class="tchiboNav">
        <? if ($rootCategoryInMenu): ?>
            <a href="<?= rtrim($rootCategoryInMenu->getLink(), '/') ?>">
                <div class="tchiboNav__title"></div>
            </a>
        <? else: ?>
            <div class="tchiboNav__title"></div>
        <? endif ?>


        <ul class="tchiboNav__list clearfix">
        <? foreach ($categories as $category):
            $active = $currentCategory && in_array($category->getId(), [$currentCategory->getParentId(), $currentCategory->getId()]) ? true : false; ?>

            <li class="item jsItemListTchibo<? if ($active): ?> active<? endif ?>">
                <a class="link" href="<?= $category->getLink() ?>">
                    <span class="itemText"><?= $category->getName() ?></span>
                </a>

                <? if ((bool)$category->getChild() && ($active || !$currentCategory)): ?>
                    <ul class="tchiboNav__sublist mDefault<? if ($active): ?> active<? endif ?>">
                    <? foreach ($category->getChild() as $child):
                        $activeChild = $currentCategory && ($child->getId() === $currentCategory->getId()) ? true : false; ?>

                        <li class="sublistItem jsItemListTchibo<? if ($activeChild): ?> mActive<? endif ?>"><a class="link" href="<?= $child->getLink() ?>"><?= $child->getName() ?></a></li>
                    <? endforeach ?>
                    </ul>
                <? endif ?>
            </li>
        <? endforeach ?>
        </ul>
    </div>
    <!--/ TCHIBO - слайдер-меню разделов Чибо -->

<? };