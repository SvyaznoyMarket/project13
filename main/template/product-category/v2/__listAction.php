<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Sorting $productSorting,
    \Iterator\EntityPager $pager,
    \Model\Product\Category\Entity $category = null
) {
    $chosenTestCase = \App::abTest()->getTest('siteListingWithViewSwitcher')->getChosenCase()->getKey();
    $chosenCategoryView = \App::request()->cookies->get('categoryView');
?>

    <!-- Сортировка товаров на странице -->
    <div class="sorting sorting-top clearfix js-category-sortingAndPagination">
        <?= $helper->render('product-category/v2/__sorting', ['productSorting' => $productSorting]) // сортировка ?>

        <? if (in_array($chosenTestCase, ['compactWithSwitcher', 'expandedWithSwitcher'], true) && $category && $category->isInSiteListingWithViewSwitcherAbTest()): ?>
            <div class="lstn-type__choose">
                <div class="lstn-type-btn lstn-type-btn--bar js-category-viewSwitcher-link <? if ($chosenTestCase === 'compactWithSwitcher' && $chosenCategoryView !== 'expanded' ||  $chosenCategoryView === 'compact'): ?>active<? endif ?>"><i class="lstn-type-icon bar"></i>Плитка</div>
                <div class="lstn-type-btn lstn-type-btn--list js-category-viewSwitcher-link js-category-viewSwitcher-link-expanded <? if ($chosenTestCase === 'expandedWithSwitcher' && $chosenCategoryView !== 'compact' ||  $chosenCategoryView === 'expanded'): ?>active<? endif ?>"><i class="lstn-type-icon list"></i>Список</div>
            </div>
        <? endif ?>

        <?= $helper->render('product-category/v2/__pagination', ['pager' => $pager, 'category' => $category]) // листалка ?>
    </div>
    <!-- /Сортировка товаров на странице -->

<? };