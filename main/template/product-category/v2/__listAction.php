<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Sorting $productSorting,
    \Iterator\EntityPager $pager,
    \Model\Product\Category\Entity $category = null
) {
?>

    <!-- Сортировка товаров на странице -->
    <div class="sorting sorting-top clearfix js-category-sortingAndPagination">
        <?= $helper->render('product-category/v2/__sorting', ['productSorting' => $productSorting]) // сортировка ?>

        <? if ($category && $category->config->listingDisplaySwitch): ?>
            <div class="lstn-type__choose">
                <div class="lstn-type-btn lstn-type-btn--bar js-category-viewSwitcher-link <? if ($category->listingView->isMosaic): ?>active<? endif ?>"><i class="lstn-type-icon bar"></i></div>
                <div class="lstn-type-btn lstn-type-btn--list js-category-viewSwitcher-link js-category-viewSwitcher-link-expanded <? if ($category->listingView->isList): ?>active<? endif ?>"><i class="lstn-type-icon list"></i></div>
            </div>
        <? endif ?>


        <?= $helper->render('product-category/v2/__pagination', ['pager' => $pager, 'category' => $category]) // листалка ?>

    </div>
    <!-- /Сортировка товаров на странице -->

<? };