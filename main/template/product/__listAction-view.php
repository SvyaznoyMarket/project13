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
    <ul class="bSortingList mViewer js-category-viewer">
        <li class="bSortingList__eItem mTitle">Вид</li>

        <li class="bSortingList__eItem mSortItem<? if ('compact' == $view): ?> mActive js-category-viewer-activeItem<? endif ?> js-category-viewer-item" data-type="compact">
            <a title="Компактный режим просмотра" class="bSortingList__eLink mTable jsChangeView" href="<?= $helper->replacedUrl(['view' => 'compact'], $excluded) ?>"><span class="bIco mIcoTable"></span></a>
        </li>
        <li class="bSortingList__eItem mSortItem<? if ('expanded' == $view): ?> mActive js-category-viewer-activeItem<? endif ?> js-category-viewer-item" data-type="expanded">
            <a title="Расширенный режим просмотра" class="bSortingList__eLink mLine jsChangeView" href="<?= $helper->replacedUrl(['view' => 'expanded'], $excluded) ?>"><span class="bIco mIcoLine"></span></a>
        </li>
    </ul>
    <!-- /Выбор варианта отображения списка товаров на странице -->

<? };
