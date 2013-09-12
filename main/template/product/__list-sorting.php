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

<? };
