<?php
/**
 * @param \Model\Product\Category\Entity[] $categories
 */
return function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Filter $productFilter,
    $openFilter,
    array $promoStyle = [],
    array $categories = []
) {

    /** @var \Model\Product\Filter\Entity[] $alwaysShowFilters */
    $alwaysShowFilters = [];
    /** @var \Model\Product\Filter\Entity[] $otherFilters */
    $otherFilters = [];
    /** @var \Model\Product\Filter\Entity $priceFilter */
    $priceFilter = null;

    $insertCustomFilters = function() use (&$categories, &$otherFilters) {
        // фильтр "Товары по категориям"
        if ((bool)$categories) {
            $categoryFilter = new \Model\Product\Filter\Entity();
            $categoryFilter->setId('category');
            $categoryFilter->setTypeId(\Model\Product\Filter\Entity::TYPE_LIST);
            $categoryFilter->setName('Товары по категориям');
            $categoryFilter->getIsInList(true);

            foreach ($categories as $category) {
                $option = new \Model\Product\Filter\Option\Entity();
                $option->setId($category->getId());
                $option->setName($category->getName());
                $categoryFilter->addOption($option);
            }

            $otherFilters[] = $categoryFilter;
        }

    };

    $countFilters = count($productFilter->getFilterCollection());
    $countInListFilters = null;
    if (0 == $countFilters) {
        $insertCustomFilters();
    } else {
        $insertIndex = $countFilters > 3 ? 3 : $countFilters;
        $i = 1;
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
                $i++;
            }

            if ($insertIndex == $i) {
                $insertCustomFilters();
                $i++;
            }

            if ($filter->getIsInList()){
                $countInListFilters++;
            }
        }
    }

    if (0 == $countInListFilters) return;
    ?>

    <div class="fltr">
        <form id="productCatalog-filter-form" class="bFilter clearfix js-category-filter js-category-filter-v3" action="?" method="GET">
            <? // Для IE9 (чтобы он отправлял форму при нажатии на клавишу enter в текстовом поле ввода) ?>
            <div style="overflow: hidden; position: absolute; top: 0; left: 0; width: 0; height: 0;"><input type="submit" /></div>

            <? foreach ($alwaysShowFilters as $filter): ?>
                <div class="fltrSet <? if (!$filter->isOpenByDefault): ?>fltrSet-close<? endif ?> js-category-filter-alwaysShowFilter js-category-filter-toggle-container <? if ('Металл' === $filter->getName()): ?>fltrSet-metall<? endif ?> <? if ('Вставка' === $filter->getName()): ?>fltrSet-insertion<? endif ?>" data-name="<?= $helper->escape($filter->getName()) ?>">
                    <div class="fltrSet_tggl <? if ($filter->isOpenByDefault): ?>fltrSet_tggl-dn<? endif ?> js-category-filter-toggle-button">
                        <span class="fltrSet_tggl_tx"><?= $helper->escape($filter->getName()) ?></span>
                    </div>

                    <div class="fltrSet_cnt js-category-filter-toggle-content" <? if (!$filter->isOpenByDefault): ?>style="display: none;"<? endif ?>>
                        <div class="fltrSet_inn clearfix">
                            <?= $helper->render('product-category/filter/__element', ['productFilter' => $productFilter, 'filter' => $filter, 'promoStyle' => $promoStyle]) ?>
                        </div>
                    </div>
                </div>
            <? endforeach ?>

            <? if ($priceFilter && $productFilter): ?>
                <div class="flrtBox">
                    <?= $helper->render('product-category/filter/element/__slider', ['productFilter' => $productFilter, 'filter' => $priceFilter, 'promoStyle' => $promoStyle]) ?>
                </div>
            <? endif ?>

            <? if ($otherFilters): ?>
                <div class="bFilterHead"<? if(!empty($promoStyle['bFilterHead'])): ?> style="<?= $promoStyle['bFilterHead'] ?>"<? endif ?>>
                    <div class="fltrSet_tggl <?= $openFilter ? 'fltrSet_tggl-dn' : '' ?> js-category-filter-otherParamsToggleButton">
                        <span class="fltrSet_tggl_tx">Ещё параметры</span>
                    </div>
                </div>
            <? endif ?>

            <div class="fltrSet" style="padding-top: 0;">
                <!-- Фильтр по выбранным параметрам -->
                <div class="bFilterCont clearfix js-category-filter-otherParamsContent" <? if (!$openFilter): ?>style="display: none"<? endif ?>>
                    <!-- Список названий параметров -->
                    <ul class="bFilterParams">
                        <? $i = 0; foreach ($otherFilters as $filter): ?>
                            <? $viewId = \View\Id::productCategoryFilter($filter->getTypeId() . '-' . $filter->getId()); ?>
                            <li class="bFilterParams__eItem<? if (0 == $i): ?> mActive<? endif ?> js-category-filter-param" data-ref="<?= $viewId ?>">
                                <span class="bParamName"><?= $filter->getName() ?></span>
                            </li>
                            <? $i++; endforeach ?>
                    </ul>
                    <!-- /Список названий параметров -->

                    <!-- Список значений параметров -->
                    <div class="bFilterValues clearfix">
                        <? $i = 0; ?>
                        <? foreach ($otherFilters as $filter): ?>
                            <div class="bFilterValuesItem clearfix<? if ($i > 0): ?> hf<? endif ?><? if (in_array($filter->getId(), ['shop', 'category'])): ?> mLineItem<? endif ?> js-category-filter-element" data-name="<?= $helper->escape($filter->getName()) ?>" id="<?= \View\Id::productCategoryFilter($filter->getTypeId() . '-' . $filter->getId()) ?>">
                                <?= $helper->render('product-category/filter/__element', ['productFilter' => $productFilter, 'filter' => $filter, 'promoStyle' => $promoStyle]) ?>
                            </div>
                            <? $i++; ?>
                        <? endforeach ?>
                    </div>
                    <!-- /Список значений параметров -->
                </div>
                <!-- /Фильтр по выбранным параметрам -->

                <?= $helper->render('product-category/__selectedFilter', ['productFilter' => $productFilter]) ?>
            </div>
        </form>
    </div>

<? };