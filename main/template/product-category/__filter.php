<?php

/**
 * @param \Helper\TemplateHelper $helper
 * @param \Model\Product\Filter $productFilter
 * @param $baseUrl
 * @param $openFilter
 * @param array $promoStyle
 * @param array $categories
 * @param \Iterator\EntityPager|null $productPager
 * @param bool|null $hasBanner
 */
$f = function(
    \Helper\TemplateHelper $helper,
    \Model\Product\Filter $productFilter,
    $baseUrl,
    $openFilter,
    array $promoStyle = [],
    array $categories = [],
    \Iterator\EntityPager $productPager = null,
    $hasBanner = null
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

    $showParamsButton = (bool) ($countInListFilters > 1 || !$priceFilter);

    $countProducts = null;
    if ($productPager && (bool)$productFilter->getValues()) {
        $countProducts = $hasBanner ? ($productPager->count() - 1) : $productPager->count();
    }
    ?>

    <div class="fltr">
        <form id="productCatalog-filter-form" class="bFilter clearfix js-category-filter js-category-filter-v1" action="<?= $baseUrl ?>" method="GET">
            <div class="bFilterHead"<? if(!empty($promoStyle['bFilterHead'])): ?> style="<?= $promoStyle['bFilterHead'] ?>"<? endif ?>>
                <? if ($showParamsButton): ?>
                    <a class="bFilterToggle btnGrey <?= $openFilter ? 'fltrSet_tggl-dn' : '' ?> js-category-filter-otherParamsToggleButton" href="#"><!--noindex--><span class="bToggleText">Бренды и параметры</span><!--/noindex--></a>
                <? endif ?>

                <? if ($priceFilter && $productFilter): ?>
                    <?= $helper->render('product-category/filter/element/__slider', ['productFilter' => $productFilter, 'filter' => $priceFilter, 'promoStyle' => $promoStyle]) ?>
                <? endif ?>

                <div class="bBtnPick clearfix">
                    <button type="submit" class="bBtnPick__eLink mBtnGrey js-category-filter-submit">Подобрать<?= $countProducts ? " ($countProducts)" : '' ?></button>
                </div>
            </div>

            <div class="fltrSet js-category-filter-otherParams" style="padding-top: 0;">
                <!-- Фильтр по выбранным параметрам -->
                <div class="bFilterCont clearfix js-category-filter-otherParamsContent" <? if (!$openFilter): ?>style="display: none"<? endif ?>>
                    <!-- Список названий параметров -->
                    <ul class="bFilterParams">
                        <? $i = 0; foreach ($otherFilters as $filter): ?>
                            <? $viewId = \View\Id::productCategoryFilter($filter->getTypeId() . '-' . $filter->getId()); ?>
                            <li class="bFilterParams__eItem<? if (0 == $i): ?> mActive<? endif ?> js-category-filter-param" data-name="<?= $helper->escape($filter->getName()) ?>" data-ref="<?= $viewId ?>">
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

                <?= $helper->render('product-category/__selectedFilter', ['productFilter' => $productFilter, 'baseUrl' => $baseUrl]) ?>
            </div>
        </form>
    </div>

<? }; return $f;