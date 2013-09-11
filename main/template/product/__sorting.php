<?php

return function(
    \Helper\TemplateHelper $helper
) { ?>

    <!-- Сортировка товаров на странице -->
    <div class="bSortingLine clearfix">
        <!-- Сортировка товаров по параметрам -->
        <ul class="bSortingList mSorting">
            <li class="bSortingList__eItem mTitle">Сортировать</li>

            <li class="bSortingList__eItem mSortItem mActive"><a class="bSortingList__eLink" href="">Автоматически</a></li>
            <li class="bSortingList__eItem mSortItem"><a class="bSortingList__eLink" href="">Лидеры продаж</a></li>
            <li class="bSortingList__eItem mSortItem"><a class="bSortingList__eLink" href="">Новинки</a></li>
            <li class="bSortingList__eItem mSortItem"><a class="bSortingList__eLink" href="">Сначала недорогие</a></li>
            <li class="bSortingList__eItem mSortItem"><a class="bSortingList__eLink" href="">Сначала дорогие</a></li>
        </ul>
        <!-- /Сортировка товаров по параметрам -->

        <!-- Выбор варианта отображения списка товаров на странице -->
        <ul class="bSortingList mViewer">
            <li class="bSortingList__eItem mTitle">Вид</li>

            <li class="bSortingList__eItem mSortItem mActive"><a class="bSortingList__eLink mTable" href=""><span class="bIco mIcoTable"></span></a></li>
            <li class="bSortingList__eItem mSortItem"><a class="bSortingList__eLink mLine" href=""><span class="bIco mIcoLine"></span></a></li>
        </ul>
        <!-- /Выбор варианта отображения списка товаров на странице -->

        <!-- Выбор вывода товаров на странице страницами/простыней -->
        <ul class="bSortingList mPager">
            <li class="bSortingList__eItem mTitle">Страницы</li>

            <li class="bSortingList__eItem mSortItem mActive"><a class="bSortingList__eLink" href="">123</a></li>
            <li class="bSortingList__eItem mSortItem"><a class="bSortingList__eLink" href="">&#8734;</a></li>
        </ul>
        <!-- /Выбор вывода товаров на странице страницами/простыней -->
    </div>
    <!-- /Сортировка товаров на странице -->

<? };