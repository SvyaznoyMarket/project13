<?php

return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Category\Entity $category = null
) {

    if (!isset($view)) $view = \App::request()->get('view', 'compact');

    $excluded = ($category && in_array($category->getProductView(), ['compact', 'expanded']))
        ? ['view' => $category->getProductView()]
        : null;

?>

    <!-- Выбор варианта отображения списка товаров на странице -->
    <ul class="bSortingList mViewer">
        <li class="bSortingList__eItem mTitle">Вид</li>

        <li class="bSortingList__eItem mSortItem<? if ('compact' == $view): ?> mActive<? endif ?>" data-type="compact">
            <a title="Компактный режим просмотра" class="bSortingList__eLink mTable jsChangeView" href="<?= $helper->replacedUrl(['view' => 'compact'], $excluded) ?>"><span class="bIco mIcoTable"></span></a>
        </li>
        <li class="bSortingList__eItem mSortItem<? if ('expanded' == $view): ?> mActive<? endif ?>" data-type="expanded">
            <a title="Расширенный режим просмотра" class="bSortingList__eLink mLine jsChangeView" href="<?= $helper->replacedUrl(['view' => 'expanded'], $excluded) ?>"><span class="bIco mIcoLine"></span></a>
        </li>
    </ul>
    <!-- /Выбор варианта отображения списка товаров на странице -->

<? };
