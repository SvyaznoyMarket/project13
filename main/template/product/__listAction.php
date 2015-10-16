<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Sorting $productSorting,
    \Iterator\EntityPager $pager,
    \Model\Product\Category\Entity $category = null
) { ?>

    <!-- Сортировка товаров на странице -->
    <div class="bSortingLine clearfix js-category-sortingAndPagination">
        <?= $helper->render('product/__listAction-sorting', ['productSorting' => $productSorting]) // сортировка ?>

        <?= $helper->render('product/__pagination', ['pager' => $pager, 'category' => $category]) // листалка ?>

    </div>
    <!-- /Сортировка товаров на странице -->

<? };