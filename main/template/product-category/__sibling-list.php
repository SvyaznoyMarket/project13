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

    <!-- TCHIBO - слайдер-меню разделов Чибо -->
    <div class="tchiboNav clearfix">
        <? if ($rootCategoryInMenu): ?>
            <a class="tchiboNav_t" href="<?= rtrim($rootCategoryInMenu->getLink(), '/') ?>">
                <div class="tchiboNav_t_img"<? if ((bool)$rootCategoryInMenuImage): ?> style="background-image: url(<?= $rootCategoryInMenuImage ?>)" <? endif ?>></div>
            </a>
        <? else: ?>
            <div class="tchiboNav_t"<? if ((bool)$rootCategoryInMenuImage): ?> style="background-image: url(<?= $rootCategoryInMenuImage ?>)" <? endif ?>></div>
        <? endif ?>


        <ul class="tchiboNav_lst clearfix">
        <? $i = 0; foreach ($categories as $category):
            $last = (count($categories) - ($i++)) <= 1; ?>

            <li class="tchiboNav_lst_i<? if ($last): ?> tchiboNav_lst_i-last<? endif ?>">
                <a class="tchiboNav_lst_lk" href="<?= $category->getLink() ?>"<? if (in_array($category->getId(), array_keys($tchiboMenuCategoryNameStyles))): ?> style="<?= $tchiboMenuCategoryNameStyles[$category->getId()] ?>"<? endif ?>>
                    <span class="tchiboNav_lst_tx"><?= $category->getName() ?></span>
                </a>

                <? if ((bool)$category->getChild()): ?>
                    <ul class="tchiboNav_slst<? if (!$last): ?> tchiboNav_slst-def<? endif ?><? if ($last): ?> tchiboNav_slst-last<? endif ?>">
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

                        <li class="tchiboNav_slst_i jsItemListTchibo<? if ($activeChild): ?> tchiboNav_slst_i-act<? endif ?> <?= $newCategory ? 'new' : '' ?>">
                            <a class="tchiboNav_slst_lk" href="<?= $child->getLink() ?>"<? if (in_array($child->getId(), array_keys($tchiboMenuCategoryNameStyles))): ?> style="<?= $tchiboMenuCategoryNameStyles[$child->getId()] ?>"<? endif ?>><?= $child->getName() ?></a>
                            <? if ($child->isNew): // SITE-3809 ?>
                                <span class="itemNew">NEW!</span>
                            <? endif ?>
                        </li>
                    <? endforeach ?>
                    </ul>
                <? endif ?>
            </li>
        <? endforeach ?>
        </ul>
    </div>
    <!--/ TCHIBO - слайдер-меню разделов Чибо -->

<? };