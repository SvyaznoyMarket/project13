<?php

return function(
    \Helper\TemplateHelper $helper,
    array $categories,
    $catalogConfig = [],
    \Model\Product\Category\Entity $currentCategory = null,
    \Model\Product\Category\TreeEntity $rootCategoryInMenu = null,
    $tchiboMenuCategoryNameStyles = [],
    $rootCategoryInMenuImage = null
) {
    /** @var $categories \Model\Product\Category\TreeEntity[] */
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
            <li class="tchibo-nav-list__item tchibo-nav-list__item_title">Коллекции</li>
        <? $i = 0; foreach ($categories as $category):
            $last = (count($categories) - ($i++)) <= 1; ?>

            <li class="tchibo-nav-list__item nav-default__item nav-default__item_child">
                <a class="tchibo-nav-list__link nav-default__link" href="<?= $category->getLink() ?>"<? if (in_array($category->getId(), array_keys($tchiboMenuCategoryNameStyles))): ?> style="<?= $tchiboMenuCategoryNameStyles[$category->getId()] ?>"<? endif ?>>
                    <?= $category->getName() ?>
                </a>

                <? if ((bool)$category->getChild()): ?>
                    <ul class="tchibo-nav-list-sub nav-default-sub">
                    <? foreach ($category->getChild() as $child):
                        $activeChild = $currentCategory && ($child->getId() === $currentCategory->getId());
                        // Шильдик NEW
                        $newCategory = false;
                        if (isset($catalogConfig['category_timing'])
                            && is_array($catalogConfig['category_timing'])
                            && in_array($child->getToken(), array_keys($catalogConfig['category_timing']), false)) {

                            $catalogTiming = $catalogConfig['category_timing'][$child->getToken()];
                            $until = strtotime($catalogTiming['until']);
                            if (time() < $until) {
                                if ($catalogTiming['type'] === 'new') $newCategory = true;
                            }
                        }?>

                        <li class="tchibo-nav-list-sub__item nav-default-sub__item <? if ($activeChild): ?> tchibo-nav-list-sub__item_active<? endif ?> <?= $newCategory ? 'new' : '' ?>">
                            <a class="tchibo-nav-list-sub__link nav-default-sub__link" href="<?= $child->getLink() ?>"<? if (in_array($child->getId(), array_keys($tchiboMenuCategoryNameStyles))): ?> style="<?= $tchiboMenuCategoryNameStyles[$child->getId()] ?>"<? endif ?>><?= $child->getName() ?></a>
                            <? if ($child->isNew): // SITE-3809 ?>
                                <span class="itemNew">NEW!</span>
                            <? endif ?>

                            <ul class="tchibo-nav-list-sub2 nav-default-sub2">
                                <li class="tchibo-nav-list-sub2__item nav-default-sub2__item">
                                    <a class="tchibo-nav-list-sub2__link nav-default-sub2__link" href="">Какой второй уровень Чибы</a>
                                </li>
                            </ul>
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