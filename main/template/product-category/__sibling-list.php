<?php

use Model\Product\Category\Entity as Category;

return function(
    \Helper\TemplateHelper $helper,
    array $categories,
    \Model\Product\Category\Entity $currentCategory = null,
    \Model\Product\Category\TreeEntity $rootCategoryInMenu = null,
    $tchiboMenuCategoryNameStyles = [],
    $rootCategoryInMenuImage = null
) {
    /** @var $categories \Model\Product\Category\TreeEntity[] */
    $categoryUids = $currentCategory
        ? array_map(function (Category $cat) { return $cat->getUi(); }, $currentCategory->getAncestor())
        : [];

    $currentCategory = $currentCategory ? : new \Model\Product\Category\Entity([]);
?>
    <? if ($rootCategoryInMenu && 'tchibo' === $rootCategoryInMenu->getToken() && 0 == $rootCategoryInMenu->getProductCount()): ?>
        <img src="//content.enter.ru/wp-content/uploads/2014/04/Tch_out.jpg" alt="К сожалению, товары Tchibo недоступны к покупке в вашем городе" />
    <? endif ?>

    <!-- Меню разделов Чибо -->
    <div class="tchibo-nav clearfix">
        <? if ($rootCategoryInMenu): ?>
            <a class="tchibo-nav__title" href="<?= rtrim($rootCategoryInMenu->getLink(), '/') ?>"></a>
        <? else: ?>
            <div class="tchibo-nav__title"<? if ((bool)$rootCategoryInMenuImage): ?> style="background-image: url(<?= $rootCategoryInMenuImage ?>)" <? endif ?>></div>
        <? endif ?>

        <ul class="tchibo-nav-list nav-default">

        <? foreach ($categories as $category): ?>

            <? if ($currentCategory->getUi() === $category->getUi() || in_array($category->getUi(), $categoryUids, true)) {
                $activeRootCategoryClass = 'tchibo-nav-list__item_active';
            } else {
                $activeRootCategoryClass = null;
            } ?>

            <?  $liClass = 'nav-default__item_child ' . $activeRootCategoryClass;
                if ($category->getUi() === Category::UI_TCHIBO_COLLECTIONS) $liClass = 'tchibo-nav-list__item_title';
                if ($category->getUi() === Category::UI_TCHIBO_SALE) $liClass = 'nav-default__item_child tchibo-nav-list__item_sale';
            ?>

            <li class="tchibo-nav-list__item nav-default__item <?= $liClass ?>">

                <a class="tchibo-nav-list__link nav-default__link" href="<?= $category->getLink() ?>">
                    <?= $category->getName() ?>
                </a>

                <? if ((bool)$category->getChild()): ?>
                    <ul class="tchibo-nav-list-sub nav-default-sub">
                    <? foreach ($category->getChild() as $child):
                        $activeChild = $currentCategory && ($child->getId() === $currentCategory->getId());
                        ?>

                        <li class="tchibo-nav-list-sub__item nav-default-sub__item <? if ($activeChild): ?> tchibo-nav-list-sub__item_active<? endif ?>">
                            <a class="tchibo-nav-list-sub__link nav-default-sub__link"
                               href="<?= $child->getLink() ?>"<? if (in_array($child->getId(), array_keys($tchiboMenuCategoryNameStyles))): ?> style="<?= $tchiboMenuCategoryNameStyles[$child->getId()] ?>"<? endif ?>><?= $child->getName() ?></a>

                            <? if ($child->getChild()) : ?>
                                <!-- Третий уровень -->
                                <ul class="tchibo-nav-list-sub2 nav-default-sub2">
                                    <? foreach ($child->getChild() as $subChild) : ?>
                                        <li class="tchibo-nav-list-sub2__item nav-default-sub2__item">
                                            <a class="tchibo-nav-list-sub2__link nav-default-sub2__link"
                                               href="<?= $subChild->getLink() ?>">
                                                <?= $subChild->getName() ?>
                                            </a>
                                        </li>
                                    <? endforeach ?>
                                </ul>
                            <? endif ?>

                        </li>
                    <? endforeach ?>
                    </ul>
                <? endif ?>
            </li>
        <? endforeach ?>
        </ul>
    </div>
    <!--/ END Млайдер-меню разделов Чибо -->
<? };