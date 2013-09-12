<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Sorting $productSorting
) {

    $list = [];

    $active = $productSorting->getActive();
    $active['url'] = $helper->replacedUrl(['sort' => implode('-', [$active['name'], $active['direction']])]);

    if ($active['name'] == 'default' && !empty($inSearch)) {
        $active['url'] = $helper->replacedUrl(['sort' => null]);
    }

    foreach ($productSorting->getAll() as $item) {
        $item['url'] = $helper->replacedUrl(array('page' => '1', 'sort' => implode('-', [$item['name'], $item['direction']])));

        if ($item['name'] == 'default' && !empty($inSearch)) {
            $item['url'] = $helper->replacedUrl(array('sort' => null));
        }

        $list[] = $item;
    }
?>

    <!-- Сортировка товаров на странице -->
    <div class="bSortingLine clearfix">
        <!-- Сортировка товаров по параметрам -->
        <ul class="bSortingList mSorting">
            <li class="bSortingList__eItem mTitle">Сортировать</li>

        <? foreach ($list as $item): ?>
            <li class="bSortingList__eItem mSortItem<? if ($active['name'] == $item['name'] && $active['direction'] == $item['direction']): ?> mActive<? endif ?>">
                <a class="bSortingList__eLink" href="<?= $item['url'] ?>"><?= $item['title'] ?></a>
            </li>
        <? endforeach ?>
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