<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Sorting $productSorting,
    \Iterator\EntityPager $pager
) { ?>

    <!-- Сортировка товаров на странице -->
    <div class="sorting clearfix js-category-sortingAndPagination">
        <?= $helper->render('product-category/v2/__sorting', ['productSorting' => $productSorting]) // сортировка ?>
        <?= $helper->render('product-category/v2/__pagination', ['pager' => $pager]) // листалка ?>
    </div>
    <!-- /Сортировка товаров на странице -->

<? };