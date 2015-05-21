<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Sorting $productSorting,
    \Iterator\EntityPager $pager
) { ?>

    <!-- Сортировка товаров на странице -->
    <div class="sorting sorting-top clearfix js-category-sortingAndPagination">
        <?= $helper->render('product-category/v2/__sorting', ['productSorting' => $productSorting]) // сортировка ?>
        <div class="lstn-type__choose">
            <div class="lstn-type-btn lstn-type-btn--bar"><i class="lstn-type-icon bar"></i>Плитка</div>
            <div class="lstn-type-btn lstn-type-btn--list active"><i class="lstn-type-icon list"></i>Список</div>
        </div>
        <?= $helper->render('product-category/v2/__pagination', ['pager' => $pager]) // листалка ?>
    </div>
    <!-- /Сортировка товаров на странице -->

<? };