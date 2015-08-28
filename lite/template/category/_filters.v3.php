<?
return function(\Model\Product\Filter $productFilter, $openFilter, $baseUrl) {
    $helper = \App::helper();
    /** @var \Model\Product\Filter\Entity[] $alwaysShowFilters */
    $alwaysShowFilters = [];
    /** @var \Model\Product\Filter\Entity[] $otherFilters */
    $otherFilters = [];
    /** @var \Model\Product\Filter\Entity $priceFilter */
    $priceFilter = null;

    $countFilters = count($productFilter->getFilterCollection());
    $countInListFilters = null;
    if (0 != $countFilters) {
        $countInListFilters = 0;
        foreach ($productFilter->getFilterCollection() as $filter) {
            if (!$filter->getIsInList()) {
                continue;
            } else if ($filter->isPrice()) {
                $priceFilter = $filter;
                $priceFilter->setStepType('price');
            } else if ($filter->getIsAlwaysShow()) {
                $alwaysShowFilters[] = $filter;
            } else {
                $otherFilters[] = $filter;
            }

            if ($filter->getIsInList()){
                $countInListFilters++;
            }
        }
    }

    if (0 == $countInListFilters) return;
    ?>

    <div class="fltr">
        <form id="productCatalog-filter-form" class="bFilter clearfix js-category-filter js-category-filter" action="<?= $baseUrl ?>" method="GET">
            <? // Для IE9 (чтобы он отправлял форму при нажатии на клавишу enter в текстовом поле ввода) ?>
            <div style="overflow: hidden; position: absolute; top: 0; left: 0; width: 0; height: 0;"><input type="submit" /></div>

            <? foreach ($alwaysShowFilters as $filter): ?>
                <div class="fltrSet <? if (!$filter->isOpenByDefault): ?>fltrSet-close<? endif ?> js-category-filter-toggle-container <? if ('Металл' === $filter->getName()): ?>fltrSet-metall<? endif ?> <? if ('Вставка' === $filter->getName()): ?>fltrSet-insertion<? endif ?>">
                    <div class="fltrSet_tggl <? if ($filter->isOpenByDefault): ?>fltrSet_tggl-dn<? endif ?> js-category-filter-toggle-button">
                        <span class="fltrSet_tggl_tx"><?= $helper->escape($filter->getName()) ?></span>
                    </div>

                    <div class="fltrSet_cnt js-category-filter-toggle-content" <? if (!$filter->isOpenByDefault): ?>style="display: none;"<? endif ?>>
                        <div class="fltrSet_inn clearfix">
                            <?= $helper->render('category/filters/__element', ['productFilter' => $productFilter, 'filter' => $filter]) ?>
                        </div>
                    </div>
                </div>
            <? endforeach ?>

            <? if ($priceFilter && $productFilter): ?>
                <div class="flrtBox">
                    <?= $helper->render('category/filters/__element', ['productFilter' => $productFilter, 'filter' => $priceFilter]) ?>
                </div>
            <? endif ?>

            <? if ($otherFilters): ?>
                <div class="bFilterHead">
                    <div class="fltrSet_tggl <?= $openFilter ? 'fltrSet_tggl-dn' : '' ?> js-category-filter-otherParamsToggleButton">
                        <span class="fltrSet_tggl_tx">Ещё параметры</span>
                    </div>
                </div>
            <? endif ?>

            <div class="fltrSet js-category-filter-otherParams" style="padding-top: 0;">
                <!-- Фильтр по выбранным параметрам -->
                <div class="bFilterCont clearfix js-category-filter-otherParamsContent" <? if (!$openFilter): ?>style="display: none"<? endif ?>>
                    <!-- Список названий параметров -->
                    <ul class="bFilterParams">
                        <? foreach ($otherFilters as $i => $filter): ?>
                            <? $viewId = \View\Id::productCategoryFilter($filter->getTypeId() . '-' . $filter->getId()); ?>
                            <li class="bFilterParams__eItem<? if (0 == $i): ?> mActive<? endif ?> js-category-filter-param" data-ref="<?= $viewId ?>">
                                <span class="bParamName"><?= $filter->getName() ?></span>
                            </li>
                            <? endforeach ?>
                    </ul>
                    <!-- /Список названий параметров -->

                    <!-- Список значений параметров -->
                    <div class="bFilterValues clearfix">
                        <? foreach ($otherFilters as $i => $filter): ?>
                            <div class="bFilterValuesItem clearfix<? if ($i > 0): ?> hf<? endif ?><? if (in_array($filter->getId(), ['shop', 'category'])): ?> mLineItem<? endif ?> js-category-filter-element" id="<?= \View\Id::productCategoryFilter($filter->getTypeId() . '-' . $filter->getId()) ?>">
                                <?= $helper->render('category/filters/__element', ['productFilter' => $productFilter, 'filter' => $filter]) ?>
                            </div>
                        <? endforeach ?>
                    </div>
                    <!-- /Список значений параметров -->
                </div>
                <!-- /Фильтр по выбранным параметрам -->

                <?= $helper->render('category/filters/selected.filters', ['productFilter' => $productFilter, 'baseUrl' => $baseUrl]) ?>
            </div>
        </form>
    </div>

<?php }; ?>