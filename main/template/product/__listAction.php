<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Sorting $productSorting,
    \Iterator\EntityPager $pager
) { ?>

    <!-- Сортировка товаров на странице -->
    <div class="bSortingLine clearfix js-category-sortingAndPagination">
        <?= $helper->render('product/__listAction-sorting', ['productSorting' => $productSorting]) // сортировка ?>

        <?= $helper->render('product/__pagination', ['pager' => $pager]) // листалка ?>

    </div>
    <!-- /Сортировка товаров на странице -->

<? };