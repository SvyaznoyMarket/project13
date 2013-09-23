<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Sorting $productSorting,
    \Iterator\EntityPager $pager,
    \Model\Product\Category\Entity $category
) { ?>

    <!-- Сортировка товаров на странице -->
    <div class="bSortingLine clearfix">
        <?= $helper->render('product/__list-sorting', ['productSorting' => $productSorting, 'category' => $category]) // сортировка ?>

        <? if (false): ?>
            <?= $helper->render('product/__list-view', ['productSorting' => $productSorting]) // режим просмотра ?>
        <? endif ?>

        <? if (false): ?>
        <!-- Выбор вывода товаров на странице страницами/простыней -->
        <ul class="bSortingList mPager">
            <li class="bSortingList__eItem mTitle">Страницы</li>

            <li class="bSortingList__eItem mSortItem mActive"><a class="bSortingList__eLink" href="">123</a></li>
            <li class="bSortingList__eItem mSortItem"><a class="bSortingList__eLink" href="">&#8734;</a></li>
        </ul>
        <!-- /Выбор вывода товаров на странице страницами/простыней -->
        <? endif ?>

        <?= $helper->render('product/__pagination', ['pager' => $pager]) // листалка ?>

    </div>
    <!-- /Сортировка товаров на странице -->

<? };