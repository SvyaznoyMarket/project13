<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Sorting $productSorting,
    \Iterator\EntityPager $pager
) { ?>

    <!-- Сортировка товаров на странице -->
    <div class="sorting sorting-top clearfix js-category-sortingAndPagination">
        <?= $helper->render('product-category/v2/__sorting', ['productSorting' => $productSorting]) // сортировка ?>

        <? if (in_array(\App::abTest()->getTest('siteListing')->getChosenCase()->getKey(), ['compactWithSwitcher', 'expandedWithSwitcher'], true)): ?>
            <div class="lstn-type__choose">
                <div class="lstn-type-btn lstn-type-btn--bar js-category-viewLink <? if (\App::request()->cookies->get('categoryView') !== 'expanded'): ?>active<? endif ?>"><i class="lstn-type-icon bar"></i>Плитка</div>
                <div class="lstn-type-btn lstn-type-btn--list js-category-viewLink js-category-viewLink-expanded <? if (\App::request()->cookies->get('categoryView') === 'expanded'): ?>active<? endif ?>"><i class="lstn-type-icon list"></i>Список</div>
            </div>
        <? endif ?>

        <?= $helper->render('product-category/v2/__pagination', ['pager' => $pager]) // листалка ?>
    </div>
    <!-- /Сортировка товаров на странице -->

<? };