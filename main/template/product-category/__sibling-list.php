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
    <? if ($rootCategoryInMenu && "tchibo" === $rootCategoryInMenu->getToken() && 0 == $rootCategoryInMenu->getProductCount()): ?>
        <img src="http://content.enter.ru/wp-content/uploads/2014/04/Tch_out.jpg" alt="К сожалению, товары Tchibo недоступны к покупке в вашем городе" />
    <? endif ?>

    <!-- TCHIBO - слайдер-меню разделов Чибо -->
    <div class="tchiboNav clearfix">
        <? if ($rootCategoryInMenu): ?>
            <a class="tchiboNav__titleLink" href="<?= rtrim($rootCategoryInMenu->getLink(), '/') ?>"><div class="tchiboNav__title"></div></a>
        <? else: ?>
            <div class="tchiboNav__title"></div>
        <? endif ?>


        <ul class="tchiboNav__list clearfix">
        <? $i = 0; foreach ($categories as $category):
            $active = $currentCategory && in_array($category->getId(), [$currentCategory->getParentId(), $currentCategory->getId()]) ? true : false;
            $last = (count($categories) - ($i++)) <= 1; ?>

            <li class="item<? if ($active): ?> active<? endif ?><? if ($last): ?> mLast<? endif ?>">
                <a class="link" href="<?= $category->getLink() ?>">
                    <span class="itemText"><?= $category->getName() ?></span>
                </a>

                <? if ((bool)$category->getChild() && ($active || !$currentCategory)): ?>
                    <ul class="tchiboNav__sublist<? if (!$last): ?> mDefault<? endif ?><? if ($active): ?> active<? endif ?><? if ($last): ?> mLast<? endif ?>">
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